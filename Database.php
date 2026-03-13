<?php
$config = [
    "host" => "localhost",
    "dbname" => "os46",
    "username" => "root",
    "password" => "KerberOS123!@#"
];

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        global $config;
        try {
            $this->connection = new PDO(
                "mysql:host=" . $config['host'] . ";dbname=" . $config['dbname'] . ";charset=utf8mb4",
                $config['username'],
                $config['password'],
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