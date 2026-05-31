<?php
namespace App\Core;

class Database
{
    private static ?Database $instance = null;
    private ?\PDO $pdo = null;

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): \PDO
    {
        if ($this->pdo instanceof \PDO) {
            return $this->pdo;
        }

        $driver = getenv('DB_DRIVER') ?: 'mysql';
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: ($driver === 'pgsql' ? '5432' : '3306');
        $name = getenv('DB_NAME') ?: 'sistema_pdvsa';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';

        if ($driver === 'pgsql') {
            $dsn = "pgsql:host={$host};port={$port};dbname={$name}";
        } else {
            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
        }

        $options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $this->pdo = new \PDO($dsn, $user, $pass, $options);
        return $this->pdo;
    }

    public static function connection(): \PDO
    {
        return self::getInstance()->getConnection();
    }
}
