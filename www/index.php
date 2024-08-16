<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Entities\Database;
use Entities\Note;
use Repository\NoteRepository;
use Factory\NoteFactory;

$abstractNote = new Note();
$database = new Database();
$noteRepository = new NoteRepository($database);
$noteFactory = new NoteFactory();

// $note = $noteFactory->saveNote('note', 'Заголовок заметки', 'Содержимое заметки');
// $noteRepository->create($note);

// $reminder = $noteFactory->saveNote('reminder', 'Заголовок напоминания', 'Содержимое напоминания', new DateTime('2024-08-15 10:00'));
// $noteRepository->create($reminder);
if( $_SERVER["REQUEST_METHOD"] === "GET"){
    $notesJson = $noteRepository->readNote($abstractNote);
}


if(isset($_POST["title"]) && isset($_POST['content']) && $_SERVER["REQUEST_METHOD"] === "POST"){
    $note = $noteFactory->saveNote('note', $_POST["title"], $_POST['content']);
    $noteRepository->create($note);
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
                <input id="TitleInp" type="text" name="title" maxlength="60" placeholder="Введите заголовок..." title="Это ваш заголовок">
                <textarea id="NoteInp" name="content" cols="30" rows="3" placeholder="Заметка..." title="Это ваша заметка"></textarea>
                <div id="date-input-container"></div>
                <button type="submit" id="submitBut">Создать</button>
            </form>
        </main>
    </div>
    <section id="notesSection">
        <div id="noteList"><?php 
        if (isset($notesJson)){
            $notes = json_decode($notesJson);
            foreach ($notes as $note) {
                echo "<div class='note'>";
                echo "<h3 class='h3Note'>" . $note->title . "</h3>";
                echo "<p class='paragraphNote'>" . $note->content . "</p>";
                $dateText = $note->last_update ? "Дата редактирования: " . htmlspecialchars($note->last_update) : "Дата создания: " . htmlspecialchars($note->time);
                echo "<p class='dateElement'>$dateText</p>";
                echo "<span class='noteListdel' data-note-id='" . $note->id . "'>🗑️</span>";
                echo "<span class='changeButton' data-note-id='" . $note->id . "'>✏️</span>";
                echo "</div>";
            }
            echo "привет";
        }
?></div>
    </section>
    <section id="remindersSection">
        <div id="reminderList"></div>
        <div id="expiredReminderList"></div>
    </section>
    <script src="script.js"></script>
  </body>
</html>
<?php
