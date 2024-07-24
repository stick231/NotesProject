<?php
require_once "class/db.php";
require_once "class/user.php";

$database = new DataBase();
$db = $database->getConnection();

$id = $_POST["id"];

$user = new User($db);
$user->setId($id);
$user->delete();
?>