<?php

namespace Controllers;

use Entities\User;
use Entities\Database;
use Repository\UserRepository;

class AuthController {
    public function logoutAndClear() {
        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();

        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                setcookie($name, '', time()-1000);
                setcookie($name, '', time()-1000, '/');
            }
        }
    }

    public function checkUser() {
        if (isset($_COOKIE['user_id'])) { 
            if(isset($_COOKIE['just_register'])){
                echo json_encode(["register" => true, "authentication" => true, "login" => $_COOKIE['just_register']]);
                exit;
            }
            if(isset($_COOKIE['login'])){
                echo json_encode(["register" => true, "authentication" => true, "login" => $_COOKIE['login']]);
                exit;
            }
            elseif(!isset($_COOKIE['auth_user_id'])){
                echo json_encode(['register' => true, 'authentication' => false, 'login' => $_COOKIE['login']]);
                exit;
            } 
        }
        else {
            echo json_encode(['register' => false, 'authentication' => false]);
            exit;
        }
    }

    public function authenticate()
    {
        if (isset($_POST["login"]) && isset($_POST["password"])) {
            $database = new Database();
            $login = $_POST["login"];
            $password = $_POST["password"];
        
            $user = (new User())
                ->setUsername($login)
                ->setPassword($password);
        
            $userRepository = new UserRepository($database);
        
            if (!is_string($userRepository->authenticate($user))) {
                $this->redirectToHomePage();
            } else {
                $response = $userRepository->authenticate($user);
                setcookie("auth_error", $response, time() + 1800, "/");
                
    
                header("Location: /auth");
                exit; 
            }
        }
    }

    public function register()
    {
        if (isset($_POST["username"]) && isset($_POST["password"])) {
            $database = new Database();

            $username = $_POST["username"];
            $password = $_POST["password"];
            
            $user = (new User())
                ->setUsername($username)
                ->setPassword($password);
            
            $userRepository = new UserRepository($database);
            
            if ($userRepository->register($user)) {
                $this->redirectToHomePage();
            } else {
                $response = "Такой пользователь уже есть!";
                setcookie("register_error", $response, time() + 1800, "/");
                header("Location: /register"); 
                exit; 
            }
        }
    }

    public function redirectToAuth()
    {
        include 'auth.php';
        exit;
    }

    public function redirectToRegister()
    {
        include 'register.php';
        exit;
    }
    public function redirectToHomePage()
    {
        header('Location: /');
    }
}