<?php
namespace System\Helpers;

class Url
{
    public static function base()
    {
        $settings = require __DIR__ . '/../../App/Config/settings.php';
        return rtrim($settings['base_url'] ?? '/', '/');
    }

    public static function to($path = '')
    {
        return self::base() . '/' . ltrim($path, '/');
    }

    public static function current()
    {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }

    public static function asset($path)
    {
        return self::base() . '/' . ltrim($path, '/');
    }
}