<?php
namespace System\Logging;

class ErrorLogger
{
    private static string $logDir = __DIR__ . '/../../App/Logs/';
    private static string $logFile = 'error.log';

    public static function register()
    {
        $settings = require __DIR__ . '/../../App/Config/settings.php';
        if (!empty($settings['error_logging_enabled'])) {
            set_error_handler([self::class, 'handleError']);
            set_exception_handler([self::class, 'handleException']);
        }
    }

    public static function handleError($errno, $errstr, $errfile, $errline)
    {
        $msg = sprintf("[%s] ERROR %d: %s in %s on line %d\n", date('Y-m-d H:i:s'), $errno, $errstr, $errfile, $errline);
        self::writeLog($msg);
    }

    public static function handleException($exception)
    {
        $msg = sprintf("[%s] EXCEPTION: %s in %s on line %d\n", date('Y-m-d H:i:s'), $exception->getMessage(), $exception->getFile(), $exception->getLine());
        self::writeLog($msg);
    }

    public static function logRequest(string $method, string $url, int $responseCode = 200, string $extra = '')
    {
        $msg = sprintf("[%s] REQUEST %s %s => %d %s\n", date('Y-m-d H:i:s'), $method, $url, $responseCode, $extra);
        self::writeLog($msg);
    }

    private static function writeLog(string $msg)
    {
        if (!is_dir(self::$logDir)) mkdir(self::$logDir, 0777, true);
        file_put_contents(self::$logDir . self::$logFile, $msg, FILE_APPEND);
    }
}
