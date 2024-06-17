<?php
require_once "db.php";
date_default_timezone_set('Europe/Moscow');

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $title = $_POST["title"];
    $content =  $_POST["content"];
    $timeCreate = date('Y-m-d H:i:s');

    $query = "INSERT INTO note (title, content, timeCreate) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($query);

    if ($stmt) {
        $stmt->bind_param("sss", $title, $content, $timeCreate);
        if ($stmt->execute()) {
            $response = array(
                'success' => true,
                'message' => 'Запись успешно добавлена'
            );
        } else {
            $response = array(
                'success' => false,
                'message' => 'Ошибка при выполнении запроса: ' . $stmt->error
            );
        }
        $stmt->close();
    } else {
        $response = array(
            'success' => false,
            'message' => 'Ошибка при подготовке запроса: ' . $mysqli->error
        );
    }

    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    header('Content-Type: application/json');
    echo json_encode(array('success' => false, 'message' => 'Метод не разрешен'));
}
?>