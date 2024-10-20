<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Controllers\MigrationController;
use Repository\MigrationRepository;
use Entities\Database;

$database = new Database();
$migrationRepository = new MigrationRepository($database);
$migrationController = new MigrationController($migrationRepository);

if($_SERVER['REQUEST_METHOD'] === "POST"){
    if(isset($_POST["data-migration"])){
        echo $migrationController->actionUp($_POST['data-migration']);
        exit;
    }
}
else{
    require_once "../www/src/Views/migration_page.php";
}

?>