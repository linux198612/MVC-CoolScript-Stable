<?php
namespace System\Database;

class Database
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            $configPath = __DIR__ . '/../../App/Config/database.php';
            if (!file_exists($configPath)) {
                throw new \Exception('Database config file not found.');
            }
            $config = require $configPath;
            // Check for required keys
            if (
                empty($config['host']) ||
                empty($config['user']) ||
                !isset($config['pass']) ||
                empty($config['db'])
            ) {
                throw new \Exception('Database configuration is incomplete.');
            }
            self::$instance = new \mysqli(
                $config['host'],
                $config['user'],
                $config['pass'],
                $config['db']
            );
            if (self::$instance->connect_errno) {
                die('Database connection failed: ' . self::$instance->connect_error);
            }
        }
        return self::$instance;
    }
}