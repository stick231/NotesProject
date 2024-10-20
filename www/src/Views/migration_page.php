<?php
use Controllers\MigrationController;
use Repository\MigrationRepository;
use Entities\Database;

$database = new Database();
$migrationRepository = new MigrationRepository($database);
$migrationController = new MigrationController($migrationRepository);

$migrationArr = $migrationController->selectMigration();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/../../assets/style/styles_migration.css">    
    <title>Миграции</title>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>id</th>
                <th>status</th>
                <th>time</th>
                <th>time_update</th>
                <th>query</th>
                <th>action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                foreach ($migrationArr as $migration) {
    echo  "<tr>
            <th>". htmlspecialchars($migration['id']) . "</th>
            <th>". htmlspecialchars($migration['status']) ."</th>
            <th>". htmlspecialchars($migration['time']) ."</th>
            <th>". htmlspecialchars($migration['time_update']) ."</th>
            <th>". htmlspecialchars($migration['query']) . "</th>
            ".  "<th><button class=button-up data-migration-id=$migration[id]>up</button><button class=button-down data-migration-id=$migration[id]>down</button></th>" . "
           </tr>
         "  ;
                }
            ?>
        </tbody>
    </table>
    <script src="../../assets/js/scriptMigration.js"></script>
</body>
</html>
