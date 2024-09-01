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

session_start();

if(!isset($_COOKIE['user_id'])){
    header('Location: register.php');
}
elseif(!isset($_SESSION['login'])){
    header('Location: login.php');
}

$currentUrl = $_SERVER['REQUEST_URI'];
$notesJson = '';
$reminderJson = '';

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if(strpos($currentUrl, '/note') !== false){
        if(isset($_GET["editData"])){
            $noteWithId = (new Note())->setId($_GET['editData']);
            echo $noteRepository->readNote($noteWithId); 
            exit;
        }
        elseif(isset($_GET['search'])){
            $noteWithSearch = (new Note())->setSearch($_GET['search']);
            echo $noteRepository->readNote($noteWithSearch); 
            exit;    
        }
        elseif(isset($_GET['read']) && $_GET['read'] === 'note'){
            echo $noteRepository->readNote($abstractNote); 
            exit;
        }
    }
    if(strpos($currentUrl, '/reminder') !== false){
        if(isset($_GET['editData'])){
            $reminderWithId = (new Reminder())->setId($_GET['editData']);
            echo $noteRepository->readReminders($reminderWithId);
            exit;
        }
        elseif(isset($_GET['search'])){
            $noteWithSearch = (new Reminder())->setSearch($_GET['search']);
            echo $noteRepository->readReminders($noteWithSearch); 
            exit;    
        }
        elseif(isset($_GET['read']) && $_GET['read'] === 'reminder'){
            echo $noteRepository->readReminders($reminderOb); 
            exit;
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if(isset($_POST["title"]) && isset($_POST['content']) && isset($_POST['createNote'])){
        $note = $noteFactory->saveNote('note', $_POST["title"], $_POST['content']);
        header('Content-Type: application/json');
        $noteRepository->create($note);
        exit; 
    }

    if (isset($_POST['note']) && isset($_POST["id"])) {
        $noteWithId = (new Note())->setId($_POST['id']);
        $noteRepository->delete($noteWithId);
        exit; 
    }

    if(isset($_POST['expired']) && isset($_POST['id'])){
        $reminderCheck = (new Reminder())->setExpired($_POST['expired'])->setId($_POST['id']);
        $noteRepository->markExpired($reminderCheck);
        exit;
    }

    if(isset($_POST['updateNote']) && isset($_POST["title"]) && isset($_POST['content'])) {
        $noteUpdate = null;
    
        if(isset($_POST['reminder_time']) && $_POST['reminder_time'] !== null ) {
            $noteUpdate = (new Reminder())
                ->setId($_POST['id'])
                ->setTitle($_POST['title'])
                ->setContent($_POST['content'])
                ->setReminderTime($_POST["reminder_time"]);
        } else {
            $noteUpdate = (new Note())
                ->setId($_POST['id'])
                ->setTitle($_POST['title'])
                ->setContent($_POST['content']);
        }
        $noteRepository->update($noteUpdate);
        exit;
    }
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
            <!-- <div data-icon="settings"><span><img src="png/icons8-settings-48.png" alt=""><p class="text">Настройки</p></span></div>  -->
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
        </div>
    </section>
    <section id="remindersSection">
        <div id="reminderList"></div>
        <div id="expiredReminderList"></div>
    </section>
    <script src="script.js"></script>
</body>
</html>