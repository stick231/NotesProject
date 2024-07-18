<?php
require_once "db.php";
date_default_timezone_set('Europe/Moscow');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST["id"];
    $title = $_POST["title"];
    $content = $_POST["content"];
    $timeUpdate = date('Y-m-d H:i:s');
    $reminderTime = isset($_POST["reminder_time"]) ? $_POST["reminder_time"] : null;

    $currentTime = strtotime(date('Y-m-d H:i:s'));
    $reminderTimestamp = strtotime($reminderTime);

    $expired = ($reminderTimestamp > $currentTime) ? 0 : 1; 

    $query = "UPDATE note SET title = ?, content = ?, last_update = ?, reminder_time = ?, expired = ? WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ssssii", $title, $content, $timeUpdate, $reminderTime, $expired, $id);

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