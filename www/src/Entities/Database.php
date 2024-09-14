<?php

namespace Entities;

class Database{
    private $user = "user";
    private $pass = "1234";
    private $host = "mysql";
    private $db = "dbtest";
    private static $instance = null;
    public $conn;

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        try {
            $this->conn = new \PDO("mysql:host=" . $this->host . ";dbname=" . $this->db, $this->user, $this->pass);
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}