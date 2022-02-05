<?php
@include(dirname(__FILE__) . '/config.php');

// используем для подключения к базе данных MySQL 

class Database {
 
    // учетные данные базы данных 
    private $host = "localhost";
    private $db;
    public $conn;

    function __construct() {
        $config = new Config;
        $this->db = $config->getDB();
    }
    // получаем соединение с базой данных 
    public function getConnection() {
 
        $this->conn = null;
        
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db["name"], $this->db["user"],$this->db["pswd"]);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
 
        return $this->conn;
    }
}
?>