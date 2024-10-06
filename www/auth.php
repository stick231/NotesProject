<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Controllers\AuthController;

$authAction = new AuthController();

setcookie("register", 'false', time() + 3600 * 24 * 30, "/");

if (isset($_COOKIE["login"]) || isset($_COOKIE['just_register'])) {
    $authAction->redirectToHomePage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $authAction->authenticate();
}
else{
    require_once "../www/src/Views/auth_page.php";
}

if (isset($_COOKIE['auth_error'])) {
    setcookie("auth_error", '', time() + 1800, "/");
    unset($_COOKIE['auth_error']); 
}