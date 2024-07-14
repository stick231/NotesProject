<?php
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET["id"])) {
        $id = $_GET['id'];

        $query = "SELECT * FROM note WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $id);

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $note = $result->fetch_assoc();
            echo json_encode($note);
        } else {
            echo json_encode(array("error" => "Устройство не найдено"));
        }
        $stmt->close();
        exit;
    }

    $sql = "SELECT * FROM note WHERE reminder_time IS NULL";

    if (isset($_GET["search"])) {
        $search = $mysqli->real_escape_string($_GET["search"]);
        $sql .= " AND (title LIKE '%$search%' OR content LIKE '%$search%' OR time LIKE '%$search%')";
    }

    $result = $mysqli->query($sql);

    if (!$result) {
        echo "Ошибка выполнения запроса: " . $mysqli->error;
        exit;
    }

    $notes = array();
    while ($row = $result->fetch_assoc()) {
        $notes[] = $row;
    }

    echo json_encode($notes);
} else {
    echo "Недопустимый метод запроса";
    exit;
}
?>