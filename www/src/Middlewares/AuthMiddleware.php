<?php

namespace MiddleWares;

class AuthMiddleware {
    public function handle($request, callable $next) {
        if((!isset($_COOKIE['user_id']))){
            $_SESSION['register_error'] = 'Пожалуйста, пройдите регистрацию.'; 
            header("Location: /register");
            exit; 
        }
        
        if(!isset($_COOKIE['auth_user_id'])){
                $_SESSION['auth_error'] = 'Пожалуйста, войдите в систему.';
                header("Location: /auth");
                exit; 
        } 
        return $next($request);
    } 
}