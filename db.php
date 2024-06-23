<?php
date_default_timezone_set('Europe/Moscow');

$user = "root";
$pass = "";
$host = "localhost";
$db = "noteapp";

$mysqli = new mysqli($host, $user, $pass, $db);

if($mysqli->connect_error){
    die('Ошибка подключения (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
?>