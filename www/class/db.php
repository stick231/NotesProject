<?php

class DataBase
{
    private $user;
    private $pass;
    private $host;
    private $db;
    public $conn;

    function __construct()
    {
        $this->user = getenv("DB_USER");
        $this->pass = getenv("DB_PASSWORD");
        $this->host = getenv("DB_HOST");
        $this->db = getenv("DB_NAME");

        echo "DB_USER: " . $this->user . "<br>";
        echo "DB_PASSWORD: " . $this->pass . "<br>";
        echo "DB_HOST: " . $this->host . "<br>";
        echo "DB_NAME: " . $this->db . "<br>";
    }

    public function getConnection()
    {
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db, $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}

 