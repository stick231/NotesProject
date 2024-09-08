<?php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();

use Entities\User;
use Entities\Database;
use Entities\Note;
use Entities\Reminder;
use Repository\UserRepository;
use Repository\NoteRepository;
use Factory\NoteFactory;

use Phroute\Phroute\RouteCollector;
use Phroute\Phroute\Dispatcher;

$router = new RouteCollector();

$database = new Database();
$noteRepository = new NoteRepository($database);

$noteFactory = new NoteFactory();

$router->get('/', function() use ($noteRepository) {
});

$router->post('/auth/logout-and-clear', function(){
    $_SESSION = array();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    
    
    if (isset($_SERVER['HTTP_COOKIE'])) {
        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach($cookies as $cookie) {
            $parts = explode('=', $cookie);
            $name = trim($parts[0]);
            setcookie($name, '', time()-1000);
            setcookie($name, '', time()-1000, '/');
        }
    }
});

$router->get('/auth-checkuser', function(){
    if (isset($_COOKIE['user_id'])) { 
        if(isset($_SESSION['login'])){
            echo json_encode(["register" => true, "authentication" => true, "login" => $_SESSION['login']]);
            exit;
        }
        elseif(!isset($_SESSION['user_id'])){
            echo json_encode(['register' => true, 'authentication' => false]);
            exit;
        }
    } 
    else {
        echo json_encode(['register' => false, 'authentication' => false]);
        exit;
    }
});

$router->get('/auth', function() use ($database) {
    include 'auth.php';
    exit;
});

$router->post('/auth-active', function() use ($database) {
    if (isset($_POST["login"]) && isset($_POST["password"])) {
        $login = $_POST["login"];
        $password = $_POST["password"];
    
        $user = (new User())
            ->setUsername($login)
            ->setPassword($password);
    
        $userRepository = new UserRepository($database);
    
        if (!is_string($userRepository->authenticate($user))) {
            header("Location: /");
            exit; 
        } else {
            $response = $userRepository->authenticate($user);
            $_SESSION['auth_error'] = $response;
            header("Location: /auth"); 
            exit; 
        }
    }
});//перенести это в функцию в auth

$router->get('/register', function(){
    include 'register.php'; 
    exit;
});

$router->post('/register-active', function() use ($database) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'])) {
        $username = $_POST["username"];
        $password = $_POST["password"];

        $user = (new User())
            ->setUsername($username)
            ->setPassword($password);

        $userRepository = new UserRepository($database);

        if ($userRepository->register($user)) {
            header("Location: /");
            exit; 
        } else {
            $response = "Такой пользователь уже есть!";
            $_SESSION['register_error'] = $response;
            header("Location: /register"); 
            exit; 
        }
    }
});//перенести это в функцию в register

$router->get('/api/notes', function() use ($noteRepository) {
    if (isset($_GET['search'])) {
        $noteWithSearch = (new Note())->setSearch($_GET['search']);
        echo $noteRepository->readNote($noteWithSearch);
        exit;    
    }
    $note = new Note();
    echo $noteRepository->readNote($note);
    exit;
});

$router->get('/api/reminder', function() use ($noteRepository) {
    if (isset($_GET['search'])) {
        $reminderWithSearch = (new Reminder())->setSearch($_GET['search']);
        echo $noteRepository->readReminders($reminderWithSearch);
        exit;    
    }
    $reminder = new Reminder();
    echo $noteRepository->readReminders($reminder);
    exit;
});

$router->post('/', function() use ($noteRepository, $noteFactory) {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if(isset($_POST["title"]) && isset($_POST['content']) && isset($_POST['createNote'])){
            $note = $noteFactory->saveNote('note', $_POST["title"], $_POST['content']);
            echo $noteRepository->create($note);
            exit; 
        }
        
        if(isset($_POST["title"]) && isset($_POST['content']) && isset($_POST['reminder_time']) && isset($_POST['createReminder'])){
            $reminder = $noteFactory->saveNote('reminder', $_POST["title"], $_POST['content'], $_POST['reminder_time']);
            echo $noteRepository->create($reminder);
            exit; 
        }

        if (isset($_POST['note']) && isset($_POST["id"])) {
            $noteWithId = (new Note())->setId($_POST['id']);
            echo $noteRepository->delete($noteWithId);
            exit;
        }

        if(isset($_POST['expired']) && isset($_POST['id'])){
            $reminderCheck = (new Reminder())->setExpired($_POST['expired'])->setId($_POST['id']);
            echo $noteRepository->markExpired($reminderCheck);
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
            echo $noteRepository->update($noteUpdate);
            exit;
        }
    }

    http_response_code(400);
    echo json_encode(['error' => 'Неверный запрос']);
});

$router->get('/note', function() use ($noteRepository){
    if (isset($_GET['editData'])) {
        $noteWithId = (new Note())->setId($_GET['editData']);
        echo $noteRepository->readNote($noteWithId);
        exit;
    }
});

$router->get('/reminders', function() use ($noteRepository) {
    if (isset($_GET['editData'])) {
        $reminderWithId = (new Reminder())->setId($_GET['editData']);
        echo json_encode($noteRepository->readReminders($reminderWithId));
        exit;
    } 
});

$dispatcher = new Dispatcher($router->getData());

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$uri = strtok($uri, '?');

try{
    echo $dispatcher->dispatch($httpMethod, $uri);
} 
 catch (Exception $e) {
    http_response_code(404);
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>404 Not Found</title>
        <style>
            body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
            h1 { font-size: 50px; }
            p { font-size: 20px; }
        </style>
    </head>
    <body>
        <h1>404 Not Found</h1>
        <p>Запрашиваемая страница не найдена.</p>
    </body>
    </html>';
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
            <a href="/note"><div home-page='1' data-icon="note" id='div-icon-note'><span><img id="icon-note" src="png/icons8-notes-48.png" alt=""><p class="text">Заметки</p></span></div></a>
            <a href="/reminders"><div data-icon="reminders" id='div-icon-reminders'><span><img src="png/icons8-reminder-241.png" alt=""><p class="text">Напоминания</p></span></div></a>
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
        <div id="noteList"></div>
    </section>
    <section id="remindersSection">
        <div id="reminderList"></div>
        <div id="expiredReminderList"></div>
    </section>
    <script src="script.js"></script>
</body>
</html>