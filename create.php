<?php
require_once "db.php";
date_default_timezone_set('Europe/Moscow');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["title"];
    $content = $_POST["content"];
    $timeCreate = date('Y-m-d H:i:s');

    $query = "INSERT INTO note (title, content, time, last_update) VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ssss", $title, $content, $timeCreate, $timeCreate);

    if ($stmt->execute()) {
        $response = array(
            'success' => true,
            'message' => 'Заметка успешно создана',
            'timeCreate' => $timeCreate
        );
        echo json_encode($response);
    } else {
        $response = array(
            'success' => false,
            'message' => 'Ошибка при создании заметки: ' . $stmt->error
        );
        echo json_encode($response);
    }

    $stmt->close();
}
?>