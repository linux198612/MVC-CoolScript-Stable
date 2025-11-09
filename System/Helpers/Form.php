<?php
namespace System\Helpers;

use System\Security\Csrf;

class Form
{
    public static function open($action = '', $method = 'post', $attributes = [])
    {
        $settings = require __DIR__ . '/../../App/Config/settings.php';
        $attr = '';
        foreach ($attributes as $key => $value) {
            $attr .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
        }
        $form = '<form action="' . htmlspecialchars($action) . '" method="' . htmlspecialchars($method) . '"' . $attr . '>';
        if (!empty($settings['csrf_enabled']) && $settings['csrf_enabled'] === true) {
            $form .= "\n<input type=\"hidden\" name=\"csrf_token\" value=\"" . Csrf::generateToken() . "\">";
        }
        return $form;
    }

    public static function close()
    {
        return '</form>';
    }
}