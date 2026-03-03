<?php
$config = [
    "host" => "localhost",
    "dbname" => "os46",
    "username" => "root",
    "password" => "KerberOS123!@#"
];

if (!function_exists("create_connection")) {
    function create_connection() {
        global $config;
        try {
            $connection = new PDO(
                "mysql:host=" . $config['host'] . ";dbname=" . $config['dbname'] . ";charset=utf8mb4",
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            return $connection;
        } catch (PDOException $e) {
            die("Database Connection Failed: " . $e->getMessage());
        }
    }    
}
?>