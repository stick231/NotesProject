<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use Controllers\AuthController;
use Controllers\ReminderController;
use Controllers\NoteController;
use Entities\Note;
use Entities\Reminder;
use Repository\NoteRepository;
use Factory\NoteFactory;
use MiddleWares\AuthMiddleware;

$router = new Phroute\Phroute\RouteCollector(); 

$router->get('/', function() {
    $authMiddleware = new AuthMiddleware();    
     $authMiddleware->handle($_REQUEST, function() {
        require_once "src/Views/main_page.php";
     });
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

$router->any('/migration', function() use ($authController){
    $authController->redirectToMigration();
});

$database = new Entities\Database();
$noteRepository = new NoteRepository($database);

$router->post('/api/notes', function() use ($noteRepository) {
    header('Content-Type: application/json');
    $noteController = new NoteController($noteRepository);
    $noteController->readNote();
});

$router->post('/api/reminders', function() use ($noteRepository) {
    header('Content-Type: application/json');
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
    $authMiddleware = new AuthMiddleware();    
    $authMiddleware->handle($_REQUEST, function() use ($noteRepository) {
        if (isset($_GET['editData'])) {
                $noteWithId = (new Note())->setId($_GET['editData']);
                echo $noteRepository->readNote($noteWithId);
                exit;
        }
        else{
            require_once "src/Views/main_page.php";
        }
    });
});

$router->get('/reminders', function() use ($noteRepository) {
    $authMiddleware = new AuthMiddleware();    
    $authMiddleware->handle($_REQUEST, function() use ($noteRepository) {
        if (isset($_GET['editData'])) {
            $reminderWithId = (new Reminder())->setId($_GET['editData']);
            echo $noteRepository->readReminders($reminderWithId);
            exit;
        } 
        else{
            require_once "src/Views/main_page.php";
        }
    });
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
    exit;
}