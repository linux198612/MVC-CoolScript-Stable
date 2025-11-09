<?php
namespace System\Middleware;

class Middleware
{
    private static array $middlewares = [];

    // Register a middleware callback (callable)
    public static function register(callable $middleware)
    {
        self::$middlewares[] = $middleware;
    }

    // Run all registered middleware, stop if any returns false
    public static function run()
    {
        foreach (self::$middlewares as $middleware) {
            if ($middleware() === false) {
                return false;
            }
        }
        return true;
    }

    // Register default middlewares based on settings
    public static function registerDefaults()
    {
        $settings = require __DIR__ . '/../../App/Config/settings.php';

        // Auth middleware (example: protect /dashboard)
        self::register(function() {
            $url = $_GET['url'] ?? '';
            if (strpos($url, 'dashboard') === 0 && empty($_SESSION['user_id'])) {
                header('Location: /login');
                return false;
            }
            return true;
        });

        // Logging middleware
        if (!empty($settings['logging_enabled'])) {
            self::register(function() {
                $logDir = __DIR__ . '/../../App/Logs/';
                if (!is_dir($logDir)) mkdir($logDir, 0777, true);
                $logFile = $logDir . 'access.log';
                $log = sprintf("[%s] %s %s\n", date('Y-m-d H:i:s'), $_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
                file_put_contents($logFile, $log, FILE_APPEND);
                return true;
            });
        }

        // Rate limiting middleware (basic, per IP)
        self::register(function() {
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $file = sys_get_temp_dir() . '/mvc_rate_' . md5($ip);
            $limit = 10000; // requests per hour
            $window = 3600; // seconds
            $data = @json_decode(@file_get_contents($file), true) ?: ['count' => 0, 'start' => time()];
            if (time() - $data['start'] > $window) {
                $data = ['count' => 1, 'start' => time()];
            } else {
                $data['count']++;
            }
            file_put_contents($file, json_encode($data));
            if ($data['count'] > $limit) {
                http_response_code(429);
                echo "<div style='text-align:center;margin-top:50px;'><h1>Rate limit exceeded</h1><p>Please try again later.</p></div>";
                return false;
            }
            return true;
        });
    }
}