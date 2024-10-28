<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Controllers\MigrationController;
use Repository\MigrationRepository;
use Entities\Database;
use Entities\Migration;

$database = new Database();
$migrationRepository = new MigrationRepository($database);
$migrationController = new MigrationController($migrationRepository);

if($_SERVER['REQUEST_METHOD'] === "POST"){
    $actionMethods = $migrationController::getActionMethodsMigration();
    
    $action = null;
    foreach ($actionMethods as $key => $value) {
        if (isset($_POST[$key])) {
            $action = $value;
            break;
        }
    }

    if ($action !== null) {
        if(isset($_POST["data-migration-update"])){
            $args = (new Migration())->withQuery($_POST['migration-up'] . " | " . $_POST['migration-down'])->withStatus("Not Activated")->withId($_POST["data-migration-update"]);
        }
        if(isset($_POST["data-migration-create"]) && $_POST["data-migration-create"]){
            $args = (new Migration())->withQuery($_POST['migration-up'] . " | " . $_POST['migration-down'])->withStatus("Created");
        }

        if(intval($_POST[$key]) && !isset($args)){
            $args = (new Migration())->withId($_POST[$key]);
        }

        echo call_user_func([$migrationController, $action], $args);
        exit;
    } else {
        echo json_encode(['error' => 'Некорректный запрос для заметок.']);
    }
}

require_once "../www/src/Views/migration_page.php";
//проделать статусы
//
?>
