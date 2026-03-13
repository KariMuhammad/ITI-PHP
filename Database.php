<?php

class Database {
    private $config = [
        "host" => "127.0.0.1",
        "dbname" => "os46",
        "username" => "root",
        "password" => "KerberOS123!@#"
    ];
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=" . $this->config['host'] . ";dbname=" . $this->config['dbname'] . ";charset=utf8mb4",
                $this->config['username'],
                $this->config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die("Database Connection Failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}

if (!function_exists("create_connection")) {
    function create_connection() {
        return Database::getInstance()->getConnection();
    }
}
?>