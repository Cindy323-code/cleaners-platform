<?php
namespace Config;

/**
 * Simple mysqli singleton.
 * Adapt host / db / user / pass to your own environment.
 */
class Database
{
    private static ?Database $instance = null;   // singleton holder
    private $connection;                         // mysqli link resource

    // ======== connection parameters ========
    private string $host     = 'localhost';  // or '127.0.0.1'
    private int    $port     = 3307;         // 3306 by default
    private string $dbname   = 'CleanPlatform';
    private string $username = 'root';
    private string $password = '';

    // --- hidden constructor ---
    private function __construct()
    {
        $this->connection = mysqli_connect(
            $this->host,
            $this->username,
            $this->password,
            $this->dbname,
            $this->port
        );

        if (!$this->connection) {
            die('Database connection error: ' . mysqli_connect_error());
        }
        // optional: set charset
        mysqli_set_charset($this->connection, 'utf8mb4');
    }

    /** @return \mysqli */
    public static function getConnection()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->connection;
    }
    // ...

    // 防止从外部克隆
    private function __clone() {}

    // 防止从外部反序列化 (PHP 8 需 public)
    public function __wakeup()
    {
        throw new \Exception('Cannot unserialize singleton');
    }
}
