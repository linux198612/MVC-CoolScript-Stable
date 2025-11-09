<?php
namespace System\Core;

use System\Security\Csrf;
use System\Logging\ErrorLogger;

class Router
{
    private $routes = [];

    public function add($method, $pattern, $handler)
    {
        $method = strtoupper($method);
        $this->routes[$method][$pattern] = $handler;
    }

    public function loadRoutesFromConfig()
    {
        $routesFile = __DIR__ . '/../../App/Config/routes.php';
        if (file_exists($routesFile)) {
            $routes = require $routesFile;
            foreach ($routes as $route) {
                [$method, $pattern, $handler] = $route;
                $this->add($method, $pattern, $handler);
            }
        }
    }

    public function dispatch($url = '', $method = null)
    {
        $method = $method ?: $_SERVER['REQUEST_METHOD'];

        // --- Normalizáljuk az URL-t ---
        $parsedUrl = parse_url($url);
        $path = isset($parsedUrl['path']) ? $parsedUrl['path'] : '/';
        $path = str_replace('/index.php', '', $path);
        $path = '/' . trim($path, '/');
        if ($path === '//') $path = '/';
        if ($path !== '/' && substr($path, -1) === '/') {
            $path = rtrim($path, '/');
        }

        // --- Settings betöltése ---
        $settings = require __DIR__ . '/../../App/Config/settings.php';

        // --- CSRF ellenőrzés ---
        if (!empty($settings['csrf_enabled']) && $settings['csrf_enabled'] === true) {
            if ($method === 'POST') {
                $token = $_POST['csrf_token'] ?? '';
                if (!Csrf::validateToken($token)) {
                    http_response_code(403);
                    echo "CSRF validation failed.";
                    return;
                }
            }
        }

        // --- Route-ok ellenőrzése ---
        if (!empty($this->routes[$method])) {
            foreach ($this->routes[$method] as $pattern => $handler) {

                // --- (:num) helyettesítés ---
                if (strpos($pattern, '(:num)') !== false) {
                    $regex = '#^' . str_replace('(:num)', '([0-9]+)', $pattern) . '$#';
                    if (preg_match($regex, $path, $matches)) {
                        array_shift($matches);
                        // --- CALLBACK támogatás ---
                        if (is_callable($handler)) {
                            return $handler($_REQUEST, $_REQUEST);
                        }
                        [$controller, $action] = $handler;
                        if (class_exists($controller)) {
                            $controllerObj = new $controller();
                            if (method_exists($controllerObj, $action)) {
                                return $controllerObj->$action(...$matches);
                            } else {
                                http_response_code(404);
                                $this->show404("The requested action <b>$action</b> does not exist in <b>$controller</b>.");
                                return;
                            }
                        } else {
                            http_response_code(404);
                            $this->show404("The requested controller <b>$controller</b> does not exist.");
                            return;
                        }
                    }
                }

                // --- (:any) helyettesítés ---
                elseif (strpos($pattern, '(:any)') !== false) {
                    $regex = '#^' . str_replace('(:any)', '([^/]+)', $pattern) . '$#';
                    if (preg_match($regex, $path, $matches)) {
                        array_shift($matches);
                        // --- CALLBACK támogatás ---
                        if (is_callable($handler)) {
                            return $handler($_REQUEST, $_REQUEST);
                        }
                        [$controller, $action] = $handler;
                        if (class_exists($controller)) {
                            $controllerObj = new $controller();
                            if (method_exists($controllerObj, $action)) {
                                return $controllerObj->$action(...$matches);
                            } else {
                                http_response_code(404);
                                $this->show404("The requested action <b>$action</b> does not exist in <b>$controller</b>.");
                                return;
                            }
                        } else {
                            http_response_code(404);
                            $this->show404("The requested controller <b>$controller</b> does not exist.");
                            return;
                        }
                    }
                }

                // --- Egyszerű, paraméter nélküli route ---
                else {
                    if ($pattern === $path) {
                        // --- CALLBACK támogatás ---
                        if (is_callable($handler)) {
                            return $handler($_REQUEST, $_REQUEST);
                        }
                        [$controller, $action] = $handler;
                        if (class_exists($controller)) {
                            $controllerObj = new $controller();
                            if (method_exists($controllerObj, $action)) {
                                return $controllerObj->$action();
                            } else {
                                http_response_code(404);
                                $this->show404("The requested action <b>$action</b> does not exist in <b>$controller</b>.");
                                return;
                            }
                        } else {
                            http_response_code(404);
                            $this->show404("The requested controller <b>$controller</b> does not exist.");
                            return;
                        }
                    }
                }
            }
        }

        // --- Default controller fallback ---
        if ($path === '/') {
            $defaultController = $settings['default_controller'] ?? 'HomeController';
            $controllerClass = 'App\\Controllers\\' . $defaultController;
            if (class_exists($controllerClass)) {
                $controllerObj = new $controllerClass();
                if (method_exists($controllerObj, 'index')) {
                    return $controllerObj->index();
                } else {
                    ErrorLogger::logRequest($method, $path, 404, "Default controller index method not found");
                }
            } else {
                ErrorLogger::logRequest($method, $path, 404, "Default controller $controllerClass not found");
            }
        }

        // --- Ha semmi nem talált ---
        ErrorLogger::logRequest($method, $path, 404, "Route not found");
        http_response_code(404);
        $this->show404("The requested page <code>$path</code> was not found.");
        return null;
    }

    private function executeHandler($handler, $matches = [], $method = '', $path = '')
    {
        array_shift($matches);
        [$controller, $action] = $handler;

        if (!class_exists($controller)) {
            ErrorLogger::logRequest($method, $path, 404, "Controller not found: $controller");
            http_response_code(404);
            $this->show404("Controller <b>$controller</b> not found.");
            return;
        }

        $controllerObj = new $controller();

        if (!method_exists($controllerObj, $action)) {
            ErrorLogger::logRequest($method, $path, 404, "Method not found: $action in $controller");
            http_response_code(404);
            $this->show404("Method <b>$action</b> not found in controller <b>$controller</b>.");
            return;
        }

        return $controllerObj->$action(...$matches);
    }

    private function show404($message = '')
    {
        $viewFile = __DIR__ . '/../../App/Views/404.php';

        if (file_exists($viewFile)) {
            ob_start();
            require $viewFile;
            $content = ob_get_clean();
            $content = str_replace('</h2>', '</h2>' . ($message ? '<div class="alert alert-warning mt-3">' . $message . '</div>' : ''), $content);
            echo $content;
        } else {
            // Built-in 404 page in English
            http_response_code(404);
            echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>404 - Not Found</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; color: #333; text-align: center; padding: 60px; }
        .container { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: inline-block; padding: 40px 60px; }
        h1 { font-size: 3em; margin-bottom: 0.2em; }
        .alert { margin-top: 2em; color: #856404; background: #fff3cd; border: 1px solid #ffeeba; border-radius: 4px; padding: 1em; display: inline-block; }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <h2>Page not found</h2>'
        . ($message ? '<div class="alert">' . $message . '</div>' : '') .
        '</div>
</body>
</html>';
        }
    }
}