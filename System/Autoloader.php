<?php
spl_autoload_register(function ($class) {
    $app_prefix = 'App\\';
    $app_base_dir = __DIR__ . '/../App/';
    $system_prefix = 'System\\';
    $system_base_dir = __DIR__ . '/';

    if (strncmp($app_prefix, $class, strlen($app_prefix)) === 0) {
        $relative_class = substr($class, strlen($app_prefix));
        $file = $app_base_dir . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) require $file;
    } elseif (strncmp($system_prefix, $class, strlen($system_prefix)) === 0) {
        $relative_class = substr($class, strlen($system_prefix));
        $file = $system_base_dir . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) require $file;
    }
});
