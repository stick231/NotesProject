<?php

use class\DataBase;
use class\User;

require_once "db.php";
require_once "user.php";

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
?>