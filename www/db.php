<?php
date_default_timezone_set('Europe/Moscow');

$user = "user";
$pass = "1234";
$host = "mysql";
$db = "dbtest";

$mysqli = new mysqli($host, $user, $pass, $db);

if($mysqli->connect_error){
    die('Ошибка подключения (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
?>