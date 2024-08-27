<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Entities\Database;
use Entities\Note;
use Repository\NoteRepository;
use Factory\NoteFactory;
use Entities\Reminder;

$abstractNote = new Note();
$reminderOb = new Reminder();
$database = new Database();
$noteRepository = new NoteRepository($database);
$noteFactory = new NoteFactory();

$notesJson = '';
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $notesJson = $noteRepository->readNote($abstractNote);
    $notes = json_decode($notesJson);
    if (is_array($notes) && !empty($notes)) {
        foreach ($notes as $note) {
            // Проверяем, существует ли свойство last_update
            if (isset($note->last_update)) {
                // echo "last_update: " . $note->last_update . "<br>";
            } else {
                // echo "last_update не найдено.<br>";
            }
        }
    } else {
        echo "Нет доступных заметок.";
    }
}
if($_SERVER["REQUEST_METHOD"] === 'GET'){
    $reminderJson = $noteRepository->readReminders($reminderOb);
    $reminders = json_decode($reminderJson);
}

if (isset($_POST["title"]) && isset($_POST['content']) && $_SERVER["REQUEST_METHOD"] === "POST") {
    $note = $noteFactory->saveNote('note', $_POST["title"], $_POST['content']);
    header('Content-Type: application/json');
    $noteRepository->create($note);
    exit; 
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">    
    <link rel="icon" href="png/icons8-notes-48.png" type="image/x-icon">
    <title>Заметки</title>
</head>
<body>
    <header id="header">
        <img src="png/user-circle.png" alt="Пользователь: " id="user-img"><br>
        <div id="btn-back" title="Выйти из аккаунта"><img id="logout" src="png/logout.png"></div>
        <h1>Заметки</h1>
        <input type="text" id="search" placeholder="Поиск">
    </header>
    <div class="sidebar">
        <div class="nav-icons">
            <div data-icon="notes"><span><img id="icon-note" src="png/icons8-notes-48.png" alt=""><p class="text">Заметки</p></span></div>
            <div data-icon="reminders"><span><img src="png/icons8-reminder-241.png" alt=""><p class="text">Напоминания</p></span></div>
            <div data-icon="settings"><span><img src="png/icons8-settings-48.png" alt=""><p class="text">Настройки</p></span></div> 
        </div>
    </div>
    <div id="container">
        <main>
            <form id="noteForm">
                <input id="TitleInp" type="text" name="title" maxlength="60" placeholder="Введите заголовок..." title="Это ваш заголовок" required>
                <textarea id="NoteInp" name="content" cols="30" rows="3" placeholder="Заметка..." title="Это ваша заметка" required></textarea>
                <div id="data-input-container"></div>
                <button type="submit" id="submitBut">Создать</button>
            </form>
        </main>
    </div>
    <section id="notesSection">
        <div id="noteList">
            <?php 
            if ($notesJson) {
                $notes = json_decode($notesJson);
                foreach ($notes as $note) {
                    echo "<div class='note'>";
                    echo "<h3 class='h3Note'>" . htmlspecialchars($note->title) . "</h3>";
                    echo "<p class='paragraphNote'>" . htmlspecialchars($note->content) . "</p>";
                    if ($note->last_update) {
                        $dateNote = "Дата редактирования: " . $note->last_update;
                    } else {
                        $dateNote = "Дата создания: " . $note->time;
                    }
                    echo "<p class='dateElement'> $dateNote </p>";
                    echo "<span class='noteListdel' data-note-id='" . htmlspecialchars($note->id) . "'>🗑️</span>";
                    echo "<span class='changeButton' data-note-id='" . htmlspecialchars($note->id) . "'>✏️</span>";
                    echo "</div>";
                }
            }
            ?>
        </div>
    </section>
    <section id="remindersSection">
        <div id="reminderList"><?php 
            if ($reminders) {
                foreach ($reminders as $reminder) {
                    echo "<div class='note'>";
                    echo "<h3 class='h3Note'>" . htmlspecialchars($reminder->title) . "</h3>";
                    echo "<p class='paragraphNote'>" . htmlspecialchars($reminder->content) . "</p>";
                    echo "<p class='dateElement'>Напоминание на: $reminder->reminder_time</p>";
                    if ($reminder->last_update) {
                        $dateNote = "Дата редактирования: " . $reminder->last_update;
                    } else {
                        $dateNote = "Дата создания: " . $reminder->time;
                    }
                    echo "<p class='dateElement'> $dateNote </p>";
                    echo "<span class='noteListdel' data-note-id='" . htmlspecialchars($reminder->id) . "'>🗑️</span>";
                    echo "<span class='changeButton' data-note-id='" . htmlspecialchars($reminder->id) . "'>✏️</span>";
                    echo "</div>";
                }
            }
            ?></div>
        <div id="expiredReminderList"></div>
    </section>
    <script src="script.js"></script>
</body>
</html>