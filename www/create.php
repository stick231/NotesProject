<?php
use class\DataBase;
use class\User;

require_once "class/db.php";
require_once "class/user.php";

$database = new DataBase();
$db = $database->getConnection();
$user = new User($db);
if(isset($_POST["title"]) && isset($_POST['content'])){
    $title = $_POST['title'];
    $content = $_POST["content"];

    $reminderTime = isset($_POST["reminder_time"]) ? $_POST["reminder_time"] : null;

    $user->setTitle($title);
    $user->setContent($content);
    if ($reminderTime !== null) {
        $user->setReminderTime($reminderTime);
    }

    $create = $user->createNote();

    if ($create) {
        $response = array(
            'success' => true,
            'message' => 'Заметка успешно создана'
        );

        echo json_encode($response);
    } else {
        $response = array(
            'success' => false,
            'message' => 'Ошибка при создании заметки: ' . $stmt->error
        );
        echo json_encode($response);
    }
}
