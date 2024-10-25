<?php
namespace Repository;

use Entities\Database;
use Entities\Migration;

class MigrationRepository {
    private $pdo;

    public function __construct(Database $database) {
        $this->pdo = $database->getConnection();
        $this->ensureTableMigrationExists();
    }   

    public function ensureTableMigrationExists()
    {
        $query = "SHOW TABLES LIKE 'migration'";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            $query = "
                CREATE TABLE migration (
                    id INT PRIMARY KEY AUTO_INCREMENT, 
                    status VARCHAR(255),                 
                    time DATETIME,                       
                    time_update DATETIME,               
                    query TEXT                           
                );";
            $stmt = $this->pdo->prepare($query);

            $stmt->execute();            
        }
    }

    public function editForm(Migration $migration)
    {
        try{
            $query = "SELECT * FROM migration WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $migrationId = $migration->getId();

            $stmt->bindParam(":id", $migrationId, \PDO::PARAM_INT);

            if($stmt->execute()){
                return json_encode($stmt->fetchAll(\PDO::FETCH_ASSOC));
            }
            else{
                return json_encode(array('success' => false, 'message' => 'Произошла ошибка' . $stmt->error));
            }
        }
        catch (\PDOException $e) {
            echo "Ошибка со связью с миграцей: " . $e->getMessage();
            return false;
        }
    }

    public function searchMigrationQuery($migrationId)
    {
        $query = "SELECT query FROM migration WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $migrationId, \PDO::PARAM_INT);

        $stmt->execute();
        $migrationQuery = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $migrationQuery; 
    }

    public function createMigration(Migration $migration)
    {
        try{
            $query = "INSERT INTO migration (status, time, time_update, query) VALUES (?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($query);

            $params = array(
                $migration->getStatus(),
                date('Y-m-d H:i:s'),
                null,
                $migration->getQuery()
            );
            if ($stmt->execute($params)) {
                $response = array(
                    'success' => true,
                    'message' => 'Заметка успешно создана'
                );
        
                return json_encode($response);
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'Ошибка при создании заметки: ' . $stmt->error
                );
                return  json_encode($response);
            }
        }
        catch (\PDOException $e) {
            echo "Ошибка при создании заметки: " . $e->getMessage();
            return false;
        }
    }

    public function deleteMigration(Migration $migration)
    {
        try{
            $query = "DELETE FROM migration WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $migrationId = $migration->getId();

            $stmt->bindParam(":id", $migrationId, \PDO::PARAM_INT);

            if($stmt->execute()){
                return json_encode(array('success' => true, 'message' => 'Миграция успешно удалена'));
            }
            else{
                return json_encode(array('success' => false, 'message' => 'Произошла ошибка' . $stmt->error));
            }
        }
        catch (\PDOException $e) {
            echo "Ошибка при удаление миграции: " . $e->getMessage();
            return false;
        }
    }

    public function updateMigration(Migration $migration)
    {
        try{
            $query = "UPDATE migration SET status = ?, time_update = ?,  query = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($query);

            $params = array(
                $migration->getStatus(),
                date('Y-m-d H:i:s'),
                $migration->getQuery(),
                $migration->getId()
            );
            if ($stmt->execute($params)) {
                $response = array(
                    'success' => true,
                    'message' => 'Миграция успешно обновленна' 
                );
        
                return json_encode($response);
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'Ошибка при создании заметки: ' . $stmt->errorInfo()
                );
                return  json_encode($response);
            }
        }
        catch (\PDOException $e) {
            echo "Ошибка при создании заметки: " . $e->getMessage();
            return false;
        }
    }


    public function actionUp(Migration $migration)
    {
        $migrationQuery = $this->searchMigrationQuery($migration->getId());

        if (empty($migrationQuery)) {
            throw new \Exception("Запрос миграции не найден для ID: " . $migration->getId());
        }

        // Убираем пробелы и сохраняем в переменные
        $migrationQuery = $migrationQuery[0]['query'];

        $queries = explode('|', $migrationQuery);
        
        $queryUp = trim($queries[0]);
        
        // может быть его будущая обработка запроса перед prepare

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare($queryUp);

            $stmt->execute();

            $updateStatusQuery = "UPDATE migration SET status = 'Activated' WHERE id = :id";
            $stmtUpdate = $this->pdo->prepare($updateStatusQuery);
            $migrationId = $migration->getId();

            $stmtUpdate->bindParam(":id", $migrationId, \PDO::PARAM_INT);
            $stmtUpdate->execute();

            $this->pdo->commit();
            return json_encode(array('success' => true, 'message' => 'Миграция прошла успешно'));
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
    
            echo json_encode(array("Ошибка: " => $e->getMessage()));
        }
    }

    public function actionDown(Migration $migration)
    {       
        $migrationQuery = $this->searchMigrationQuery($migration->getId());

        if (empty($migrationQuery)) {
            throw new \Exception("Запрос миграции не найден для ID: " . $migration->getId());
        }

        // Убираем пробелы и сохраняем в переменные
        $migrationQuery = $migrationQuery[0]['query'];

        $queries = explode('|', $migrationQuery);
        
        $queryDown = trim($queries[1]);
        
        // может быть его будущая обработка запроса перед prepare

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare($queryDown);

            $stmt->execute();

            $updateStatusQuery = "UPDATE migration SET status = 'Not Activated' WHERE id = :id";
            $stmtUpdate = $this->pdo->prepare($updateStatusQuery);

            $migrationId = $migration->getId();
            $stmtUpdate->bindParam(":id", $migrationId, \PDO::PARAM_INT);
            
            $stmtUpdate->execute();

            $this->pdo->commit();
            return json_encode(array('success' => true, 'message' => 'Откат прошел успешно'));
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            echo json_encode(array("Ошибка: " => $e->getMessage()));
        }
    }
    // страничка созданий миграций 
    // прочитать про асинхронные запросы js 

    public function selectMigration()
    {
        $query = "SELECT * FROM migration";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return json_encode($stmt->fetchAll(\PDO::FETCH_ASSOC));

    }
}

//1 разработать структуру таблицы миграции которая должна id status time time_update (время изменения) sql db
//2 создать таблицу модефецировать(alert) закинуть 
//3 roolback

// view и controller 