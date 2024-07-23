<?php
require_once "db.php";
require_once "user.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $database = new DataBase();
    $db = $database->getConnection();

    $user = new User($db);

    $expired = $_POST["expired"];
    $user->setExpired($expired);

    $user->expiredReminders();
}
?>