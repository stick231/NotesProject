<?php

use class\DataBase;
use class\User;

require_once "db.php";
require_once "user.php";

$database = new DataBase();
$db = $database->getConnection();

$id = $_POST["id"];

$user = new User($db);
$user->setId($id);
$user->delete();
?>