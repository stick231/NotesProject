<?php
require_once "db.php";
date_default_timezone_set('Europe/Moscow');

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $id = $_POST["id"];
    $title = $_POST["title"];
    $content = $_POST["content"];
    $timeUpdate = date('Y-m-d H:i:s');

    $query = "UPDATE note SET title = ?, content = ?, timeCreate = ? WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("sssi", $title, $content, $timeUpdate, $id);

    if ($stmt->execute()) {
        $response = array(
            'success' => true,
            'message' => 'Устройство успешно обновлено',
            'timeUpdate' => $timeUpdate
        );
        echo json_encode($response);
    } else {
        $response = array(
            'success' => false,
            'message' => 'Ошибка при обновлении устройства: ' . $stmt->error
        );
        echo json_encode($response);
    }

    $stmt->close();
}
?>