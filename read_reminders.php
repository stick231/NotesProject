<?php
require_once "db.php";

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $sql = "SELECT * FROM note WHERE reminder_time IS NOT NULL";

    if (isset($_GET["search"])) {
        $search = $mysqli->real_escape_string($_GET["search"]);
        $sql .= " AND (title LIKE '%$search%' OR content LIKE '%$search%' OR reminder_time LIKE '%$search%')";
    }

    $result = $mysqli->query($sql);

    if (!$result) {
        echo "Ошибка выполнения запроса: " . $mysqli->error;
        exit;
    }

    $reminders = array();
    while ($row = $result->fetch_assoc()) {
        $reminders[] = $row;
    }

    echo json_encode($reminders);
} else {
    echo "Недопустимый метод запроса";
    exit;
}
?>