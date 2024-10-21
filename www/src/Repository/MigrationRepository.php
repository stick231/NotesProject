<?php
namespace Repository;

use Entities\Database;

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

    public function searchMigrationQuery($migrationId)
    {
        $query = "SELECT query FROM migration WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $migrationId, \PDO::PARAM_INT);

        $stmt->execute();
        $migrationQuery = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $migrationQuery; 
    }

    public function actionUp($migrationId)
    {
        $migrationQuery = $this->searchMigrationQuery($migrationId);

        if (empty($migrationQuery)) {
            throw new \Exception("Запрос миграции не найден для ID: " . $migrationId);
        }
        
        $migrationQuery = $migrationQuery[0]['query'];
        
        // может быть его будущая обработка запроса перед prepare

        try {
            $stmt = $this->pdo->prepare($migrationQuery);
            $stmt->execute();
            return json_encode(array('success' => true, 'message' => 'Миграция прошла успешно'));
        } catch (\PDOException $e) {
            echo json_encode(array("Ошибка: " => $e->getMessage()));
        }
    }

    public function actionDown()
    {
        
    }

    public function selectMigration()
    {
        $query = "SELECT * FROM migration";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }
}

//1 разработать структуру таблицы миграции которая должна id status time time_update (время изменения) sql db
//2 создать таблицу модефецировать(alert) закинуть 
//3 roolback

// view и controller 