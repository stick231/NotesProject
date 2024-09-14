<?php

namespace Controllers;

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
            if(isset($_SESSION['just_register'])){
                echo json_encode(["register" => true, "authentication" => true, "login" => $_SESSION['just_register']]);
                exit;
            }
            if(isset($_SESSION['login'])){
                echo json_encode(["register" => true, "authentication" => true, "login" => $_SESSION['login']]);
                exit;
            }
            elseif(!isset($_SESSION['user_id'])){
                echo json_encode(['register' => true, 'authentication' => false]);
                exit;
            } 
        }
        else {
            echo json_encode(['register' => false, 'authentication' => false]);
            exit;
        }
        exit;
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