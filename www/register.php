<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Controllers\AuthController;

$authAction = new AuthController();

if(isset($_GET['register']) && $_GET['register'] === false){
    setcookie("register", 'false', time() + 3600 * 24 * 30, "/");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $authAction->register();
}
else{
    require_once "../www/src/Views/register_page.php";
}

if (isset($_COOKIE["user_id"]) && isset($_COOKIE["register"]) && $_COOKIE["register"] === true) {
    $authAction->redirectToHomePage();
}
elseif(isset($_COOKIE["login"]) || isset($_COOKIE['just_register'])){
    $authAction->redirectToHomePage();
}

if (isset($_COOKIE['register_error'])) {
    setcookie("register_error", '', time() + 1800, "/");;
    unset($_COOKIE['register_error']); 
}