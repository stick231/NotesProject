<?php

namespace Repository;

Use Entities\Database;
use Entities\User;

class UserRepository implements UserRepositoryInterface{
    private $pdo;

    public function __construct(Database $database) {
        $this->pdo = $database->getConnection();
    }

    public function checkUser(User $user)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$user->getUsername()]);
        $existingUser = $stmt->fetch();
    
        if ($existingUser) {
            return false;
        }
        return true;
    }

    public function register(User $user) 
    {
        if($this->checkUser($user)){
            $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$user->getUsername(), $user->getEmail(), $user->getPassword()]);
        } 
        return "Такой пользователь уже есть!";
    }

    public function authenticate(User $user)
    {
        {
            $query = "SELECT * FROM users WHERE username = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(1, $user->getUsername(), \PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            if (count($result) > 0) {
                foreach($result as $row) {
                if (password_verify($user->getUsername(), $row["password"])) {
                    $userId = $row['id'];
                    setcookie("user_id", $userId, time() + 3600 * 24 * 30, "/");
                    $_SESSION["login"] = $user->getUsername();
                    setcookie("register", 'true', time() + 3600 * 24 * 30, "/");
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

    public function findByEmail($email) 
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $userData = $stmt->fetch();

        if ($userData) {
            return new User($userData['username'], $userData['email'], $userData['password']);
        }

        return null;
    }
}