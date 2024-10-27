<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/../../assets/style/styles_migration.css">    
    <title>Миграции</title>
</head>
<body>
    <main>
        <form id='formMigration' action="" method="post">
            <label for="migration-up">Миграция</label>
            <input type="text" name="migration-up" id="MigrationUp">
            <label for="migration-down">Откат</label>
            <input type="text" name="migration-down" id="MigrationDown">
            <button type="submit" id="submitBut">Сохранить</button>
        </form>
    </main>
    <section>
        <table>
            <thead>
                <tr>
                    <th>id</th>
                    <th>status</th>
                    <th>time</th>
                    <th>time_update</th>
                    <th>query</th>
                    <th>action</th>
                    <th>Manage</th>
                </tr>
            </thead>
            <tbody id="migrationTableBody">
            </tbody>
        </table>
    </section>
    <script src="../../assets/js/scriptMigration.js"></script>
</body>
</html>
