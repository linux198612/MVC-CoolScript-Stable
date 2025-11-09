<?php
namespace System\Helpers;

class Redirect
{
    public static function to($url, $status = 302)
    {
        header('Location: ' . $url, true, $status);
        exit;
    }

    public static function back()
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header('Location: ' . $referer, true, 302);
        exit;
    }
}