<?php

class DataBase
{
    private $user = "user";
    private $pass = "1234";
    private $host = "mysql";
    private $dbNote = "dbtest";
    private $dbUser = 'user';
    public $conn;

    public function getConnection()
    {
        // $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbNote, $this->user, $this->pass);
        // var_dump( $this->conn); 
        // $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // exit;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbNote, $this->user, $this->pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }

}

?> 