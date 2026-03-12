<?php


function getDbConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
       
        $host = getenv('DB_HOST') ?: 'localhost';
        $port = getenv('DB_PORT') ?: '5432';
        $dbname = getenv('DB_NAME') ?: 'wishlist_service';
        $user = getenv('DB_USER') ?: 'postgres';
        $password = getenv('DB_PASS') ?: '4321';
        
        try {
            $pdo = new PDO(
                "pgsql:host={$host};port={$port};dbname={$dbname}",
                $user,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            
            $pdo->exec("SET client_encoding = 'UTF8'");
        } catch (PDOException $e) {
            die("Ошибка подключения к базе данных: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $this->connection = getDbConnection();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
}
