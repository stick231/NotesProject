<?php
require_once "class/db.php";
require_once "class/user.php";

$database = new DataBase();
$db = $database->getConnection();

$user = new User($db);

if(isset($_GET["search"])){
    $search = $_GET["search"];

    $user->setSearch($search);
    $user->readNote();
}
else if(isset($_GET['id'])){
    $id = $_GET['id'];

    $user->setId($id);  
    $user->readNote();
}
else{
    $user->readNote();
}
