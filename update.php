<?php
require_once "db.php";

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $id = $_POST["id"];
    $title = $_POST["title"];
    $content = $_POST["content"];

    $query = "UPDATE note SET title = ?, content = ? WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ssi", $title, $content, $id);

    if ($stmt->execute()) {
        $response = array(
            'success' => true,
            'message' => 'Устройство успешно обновлено'
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