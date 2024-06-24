<?php
session_start();
header('Content-Type: application/json');

if (isset($_COOKIE['user_id'])) { 
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