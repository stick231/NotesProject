<?php
require_once "db.php";

if($_SERVER["REQUEST_METHOD"] === "POST"){
    $id = $_POST["id"];

    $query = "DELETE FROM note WHERE id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $response = array(
            'success' => true,
            'message' => 'Устройство успешно удалено'
        );
    } else {
        $response = array(
            'success' => false,
            'message' => 'Ошибка при удалении устройства: ' . $stmt->error
        );
    }

    $stmt->close();
    echo json_encode($response);
}
else{
    echo "Недопустимый метод запроса";
    exit;
}
?>