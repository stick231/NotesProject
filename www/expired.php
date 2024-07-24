<?php
use class\DataBase;
use class\User;

require_once "class/db.php";
require_once "class/user.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $database = new DataBase();
    $db = $database->getConnection();

    $user = new User($db);

    $id = $_POST['id'];
    $user->setId($id);

    $expired = $_POST["expired"];
    $user->setExpired($expired);

    $user->expiredReminders();
}
