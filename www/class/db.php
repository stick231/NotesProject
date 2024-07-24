<?php
namespace class;

class DataBase
{
    private $user = "root";
    private $pass = "";
    private $host = "localhost";
    private $dbNote = "noteapp";
    private $dbUser = 'user';
    public $conn;

    public function getConnection()
    {
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