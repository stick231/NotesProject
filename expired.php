<?php
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["id"])) {
        $id = $_POST["id"];
        $expired = $_POST["expired"] === 'true';

        if ($expired) {
            $query = "UPDATE note SET expired = TRUE WHERE id = ?";
        } else {
            $query = "UPDATE note SET expired = FALSE WHERE id = ?";
        }

        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $response = array(
                'success' => true,
                'message' => 'Напоминание устарело'
            );
        } else {
            $response = array(
                'success' => false,
                'message' => 'Ошибка при обновлении напоминания: ' . $stmt->error
            );
        }

        echo json_encode($response);
    } else {
        $response = array(
            'success' => false,
            'message' => 'ID не был передан'
        );
        echo json_encode($response);
    }
}
?>