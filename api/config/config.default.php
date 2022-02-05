<?php

class Config {
    private $DB_INFO = [
        "port" => "3306",
        "name" => "BD_NAME",
        "user" => "USER_NAME",
        "pswd" => "PASSWORD", 
    ];

    private $TIME_ZONE = "Europe/Moscow";
    private $DEBUG_MODE = true;

    public function getDB() {
        return $this->DB_INFO;
    }
    public function getTMZ() {
        return $this->TIME_ZONE;
    }
    public function getDebugMode() {
        if ($this->DEBUG_MODE) {
            ini_set('display_errors', 1);
            error_reporting(E_ALL);    
        }
        return $this->DEBUG_MODE;
    }
}
?>