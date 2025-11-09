<?php
declare(strict_types=1);

require_once __DIR__ . '/../System/Autoloader.php';

// Load config
$settings = require __DIR__ . '/../App/Config/settings.php';
require_once __DIR__ . '/../App/Config/database.php';

// Debug mode: show/hide PHP errors
if (!empty($settings['debug']) && $settings['debug'] === true) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(0);
}

// Enforce HTTPS if enabled
if (!empty($settings['force_https']) && $settings['force_https'] === true) {
    if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
        $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header('Location: ' . $redirect, true, 301);
        exit;
    }
}

error_log('REQUEST_URI = ' . ($_SERVER['REQUEST_URI'] ?? ''));
error_log('SCRIPT_NAME = ' . ($_SERVER['SCRIPT_NAME'] ?? ''));
error_log('QUERY_STRING = ' . ($_SERVER['QUERY_STRING'] ?? ''));


// Session indítása a legelején
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Start session (automatic, cookie or sql)
\System\Security\Session::start();

// Register error logger if enabled
\System\Logging\ErrorLogger::register();

// Register default middlewares
\System\Middleware\Middleware::registerDefaults();

// Run middleware before routing
if (!\System\Middleware\Middleware::run()) {
    exit;
}

// Initialize router and dispatch
use System\Core\Router;

$router = new Router();
$router->loadRoutesFromConfig();

// --- FIX: REQUEST_URI parse helyes kezelése ---
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$requestUri = str_replace('/index.php', '', $requestUri);
$content = $router->dispatch($requestUri, $_SERVER['REQUEST_METHOD']);

// Set secure HTTP headers
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer-when-downgrade");
header("X-XSS-Protection: 1; mode=block");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");

// Set Content Security Policy (CSP)
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' https://cdn.jsdelivr.net; img-src 'self' data:; font-src 'self' https://cdn.jsdelivr.net;");

// --- Kimenet csak a header-ek után ---
if ($content !== null) {
    echo $content;
}
