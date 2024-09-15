<?php

namespace Controllers;

class AuthMiddleware {
    public function handle($request, callable $next) {
        if (!isset($_COOKIE['user_id'])) { 
            if(!isset($_SESSION['user_id'])){
                $_SESSION['auth_error'] = 'Пожалуйста, войдите в систему.';
                header("Location: /auth");
            } 
        
            else {
                $_SESSION['register_error'] = 'Пожалуйста, пройдите регистрацию.'; 
                header("Location: /register");
            }
        }    
        return $next($request);
    }
}