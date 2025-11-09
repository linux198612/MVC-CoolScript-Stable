<?php
namespace System\Security;

class Csrf
{
    public static function generateToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateToken($token)
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    // Automatically inject CSRF token into all forms
    public static function injectToken($html)
    {
        $token = self::generateToken();
        // Add hidden input to every <form> (POST or GET)
        return preg_replace_callback(
            '/<form\b([^>]*)>/i',
            function ($matches) use ($token) {
                $formTag = $matches[0];
                $injected = $formTag . "\n<input type=\"hidden\" name=\"csrf_token\" value=\"$token\">";
                return $injected;
            },
            $html
        );
    }
}