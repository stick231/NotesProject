<?php
$user = "root";
$pass = "";
$db = "noteapp";
$host = "localhost";

$mysqli = new mysqli($host, $user, $pass, $db);

if($mysqli->connect_error){
    die('Ошибка подключения (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}
?>