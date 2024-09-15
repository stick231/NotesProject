<?php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();

use Controllers\AuthController;
use Controllers\ReminderController;
use Controllers\NoteController;
use Entities\Note;
use Entities\Reminder;
use Repository\NoteRepository;
use Factory\NoteFactory;
use Controllers\AuthMiddleware;

$router = new Phroute\Phroute\RouteCollector(); 

$router->get('/', function() {
    $authMiddleware = new AuthMiddleware();    
    $authMiddleware->handle($_REQUEST, function() {});
});

$authController = new AuthController();

$router->post('/auth/logout-and-clear', function() use ($authController){
    $authController->logoutAndClear();
});

$router->get('/auth-checkuser', function() use ($authController){
    $authController->checkUser();
});

$router->any('/auth', function() use ($authController){
    $authController->redirectToAuth();
});

$router->any('/register', function() use ($authController){
    $authController->redirectToRegister();
});

$database = new Entities\Database();
$noteRepository = new NoteRepository($database);

$router->get('/api/notes', function() use ($noteRepository) {
    $noteController = new NoteController($noteRepository);
    $noteController->readNote();
});

$router->get('/api/reminders', function() use ($noteRepository) {
    $reminderController = new ReminderController($noteRepository);
    $reminderController->readReminder();
});

$noteFactory = new NoteFactory(); 

$router->post('/notes', function() use ($noteRepository, $noteFactory)  {
    $authMiddleware = new AuthMiddleware();    
    $authMiddleware->handle($_REQUEST, function() use ($noteRepository, $noteFactory) {
        $noteController = new NoteController($noteRepository, $noteFactory);
        $actionMethods = NoteController::getActionMethodsNote();
    
        $action = null;
        foreach ($actionMethods as $key => $value) {
            if (isset($_POST[$key])) {
                $action = $value;
                break;
            }
        }
    
        if ($action !== null) {
            call_user_func([$noteController, $action]);
        } else {
            echo json_encode(['error' => 'Некорректный запрос для заметок.']);
        }
    });
});

$router->post('/reminders', function() use ($noteRepository, $noteFactory){
    $authMiddleware = new AuthMiddleware();    
    $authMiddleware->handle($_REQUEST, function() use ($noteRepository, $noteFactory) {
        $reminderController = new ReminderController($noteRepository, $noteFactory);
        $actionMethods = ReminderController::getActionMethodsReminder();

        $action = null;
        foreach ($actionMethods as $key => $value) {
            if (isset($_POST[$key])) {
                $action = $value;
                break;
            }
        }

        if ($action !== null) {
            call_user_func([$reminderController, $action]);
            exit;
        } else {
            echo json_encode(['error' => 'Некорректный запрос для напоминаний.']);
        }
    });
});

$router->get('/notes', function() use ($noteRepository){
    if (isset($_GET['editData'])) {
        $noteWithId = (new Note())->setId($_GET['editData']);
        echo $noteRepository->readNote($noteWithId);
        exit;
    }
});

$router->get('/reminders', function() use ($noteRepository) {
    if (isset($_GET['editData'])) {
        $reminderWithId = (new Reminder())->setId($_GET['editData']);
        echo $noteRepository->readReminders($reminderWithId);
        exit;
    } 
});

$dispatcher = new Phroute\Phroute\Dispatcher($router->getData());

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
            <a href="/notes"><div home-page='1' data-icon="notes" id='div-icon-notes'><span><img id="icon-note" src="png/icons8-notes-48.png" alt=""><p class="text">Заметки</p></span></div></a>
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