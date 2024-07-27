<?php
session_start();

class UserRegistration {
    private $username;
    private $password;
    public $dbConnection;

    public function __construct($dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function registerNewUser()
    {
        $stmt = $this->dbConnection->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$this->username]);
        $existingUser = $stmt->fetch();
    
        if ($existingUser) {
            $error = "Такой пользователь уже есть";
            return $error;
            exit;
        } else {
            $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
    
            $stmt = $this->dbConnection->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$this->username, $hashedPassword]);
    
            $userId = $this->dbConnection->lastInsertId();
            setcookie("user_id", $userId, time() + 3600 * 24 * 30, "/"); 
            setcookie("register", "true", time() + 3600 * 24 * 30, "/");
            $_SESSION['user_id'] = $userId;
    
            $_SESSION["register_username"] = $this->username;
            $_SESSION['just_registered'] = true;
            header('Location: index.html');
        }
    }

    public function authenticateUser()
    {
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->dbConnection->prepare($query);
        $stmt->bindParam(1, $this->username, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($result) > 0) {
            foreach($result as $row) {
            if (password_verify($this->password, $row["password"])) {
                $userId = $row['id'];
                setcookie("user_id", $userId, time() + 3600 * 24 * 30, "/");
                $_SESSION["login"] = $this->username;
                setcookie("register", 'true', time() + 3600 * 24 * 30, "/");
                header("location: index.html");
            } else {
                $warning = "Неверный логин или пароль";
                return $warning;
            }
        }
    }
        else {
            $warning = "Пользователь не найден";
            return $warning;
        }
    }
}