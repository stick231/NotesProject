<?php
namespace Repository;

Use Entities\Database;
use Entities\AbstractNote;
use Entities\Note;
use Entities\Reminder;

class NoteRepository implements NoteRepositoryInterface{
    private $pdo;

    public function __construct(Database $database) {
        $this->pdo = $database::getInstance()->getConnection();
    }

    public function create(AbstractNote $abstractNote)
    {
        try{
            $query = "INSERT INTO note (title, content, time, reminder_time, user_id) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($query);

            $reminderTime = null;

            if ($abstractNote instanceof Reminder) {
                $reminderTime = $abstractNote->getReminderTime();
            }

            $params = array(
                $abstractNote->getTitle(),
                $abstractNote->getContent(),
                date('Y-m-d H:i:s'),
                $reminderTime instanceof \DateTime ? $reminderTime->format('Y-m-d H:i:s') : $reminderTime,
                $_COOKIE['auth_user_id']
            );
            if ($stmt->execute($params)) {
                $response = array(
                    'success' => true,
                    'message' => 'Заметка успешно создана'
                );
        
                echo json_encode($response);
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'Ошибка при создании заметки: ' . $stmt->error
                );
                echo json_encode($response);
            }
        }
        catch (\PDOException $e) {
            echo "Ошибка при создании заметки: " . $e->getMessage();
            return false;
        }
    }

    public function readNote(Note $note)
    {   
        try{
            if ($note->getId() !== null) {
                $query = "SELECT * FROM note WHERE id = :id AND user_id = :user_id";

                $stmt = $this->pdo->prepare($query);
                $idParam = $note->getId() ;
                $userIdParams = $_COOKIE['auth_user_id'];

                $stmt->bindParam(':id', $idParam, \PDO::PARAM_INT);
                $stmt->bindParam(':user_id', $userIdParams, \PDO::PARAM_INT);
            } 
            elseif($note->getSearch() !== null){
                $query = "SELECT * FROM note WHERE reminder_time IS NULL AND (title LIKE :search OR content LIKE :search OR time LIKE :search) AND user_id = :user_id";

                $stmt = $this->pdo->prepare($query);
    
                $searchParam = "%{$note->getSearch()}%";
                $userIdParams = $_COOKIE['auth_user_id'];

                $stmt->bindParam(':search', $searchParam, \PDO::PARAM_STR);
                $stmt->bindParam(':user_id', $userIdParams, \PDO::PARAM_INT);
            }
            else {
                $query = "SELECT * FROM note WHERE reminder_time IS NULL AND user_id = :user_id";
                $stmt = $this->pdo->prepare($query);

                $userIdParams = $_COOKIE['auth_user_id'];
                $stmt->bindParam(':user_id', $userIdParams, \PDO::PARAM_INT);
            }

        $stmt->execute();
        $notes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return json_encode($notes);
        }
        catch (\PDOException $e) {
            echo "Ошибка при чтение заметки: " . $e->getMessage();
            return false;
        }
    }   

    public function readReminders(Reminder $reminder)
    {   
        try{
            if ($reminder->getId() !== null) {
                $query = "SELECT * FROM note WHERE id = :id AND user_id = :user_id";

                $stmt = $this->pdo->prepare($query);
                $idParam = $reminder->getId() ;
                $userIdParams = $_COOKIE['auth_user_id'];

                $stmt->bindParam(':id', $idParam, \PDO::PARAM_INT);
                $stmt->bindParam(':user_id', $userIdParams, \PDO::PARAM_INT);
            } 
            elseif($reminder->getSearch() !== null){
                $query = "SELECT * FROM note WHERE reminder_time IS NOT NULL AND (title LIKE :search OR content LIKE :search OR reminder_time LIKE :search) AND user_id = :user_id";

                $stmt = $this->pdo->prepare($query);
    
                $searchParam = "%{$reminder->getSearch()}%";
                $userIdParams = $_COOKIE['auth_user_id'];
                
                $stmt->bindParam(':search', $searchParam, \PDO::PARAM_STR);
                $stmt->bindParam(':user_id', $userIdParams, \PDO::PARAM_INT);
            }
            else {
                $query = "SELECT * FROM note WHERE reminder_time IS NOT NULL AND user_id = :user_id";
                $stmt = $this->pdo->prepare($query);

                $userIdParams = $_COOKIE['auth_user_id'];
                $stmt->bindParam(':user_id', $userIdParams, \PDO::PARAM_INT);
            }

            $stmt->execute();
            $reminders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            return json_encode($reminders);
        }
        catch (\PDOException $e) {
            echo "Ошибка при чтение напоминаний: " . $e->getMessage();
            return false;
            exit;
        }
    }

    public function delete(AbstractNote $abstractNote)
    {   
        try{
            $query = "DELETE FROM note WHERE id = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(1, $abstractNote->getId(), \PDO::PARAM_INT);
            if ($stmt->execute()) {
                $response = array(
                    'success' => true,
                    'message' => 'Устройство успешно удалено' 
                );
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'Ошибка при удалении устройства: ' . $stmt->error
                );
            }

            echo json_encode($response);
            }
        catch (\PDOException $e) {
            echo "Ошибка при удаление заметки: " . $e->getMessage();
            return false;
        }
    }

    public function update(AbstractNote $abstractNote)
    {
        try{
            $currentTime = strtotime(date('Y-m-d H:i:s'));
            $reminderTime = null;

            if ($abstractNote instanceof Reminder) {
                $reminderTime = $abstractNote->getReminderTime();
                $reminderTimestamp = strtotime($reminderTime);
                $expired = ($reminderTimestamp > $currentTime) ? 0 : 1; 
            }
            else{
                $expired = 0;
            }
    
            $query = "UPDATE note SET title = ?, content = ?, last_update = ?, reminder_time = ?, expired = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($query);
            $params = array(
                $abstractNote->getTitle(),
                $abstractNote->getContent(),
                date('Y-m-d H:i:s'),
                $reminderTime,
                $expired,
                $abstractNote->getId()
            );

            if ($stmt->execute($params)) {
                $response = array(
                'success' => true,
                'message' => 'Устройство успешно обновлено',
            );
            echo json_encode($response);
            } else {
                $errorInfo = $stmt->errorInfo();
                $response = array(
                    'success' => false,
                    'message' => 'Ошибка при обновлении устройства: ' . $errorInfo[2]
                );
            echo json_encode($response);
            }
        }   
        catch (\PDOException $e) {
            echo "Ошибка при обновлении заметки: " . $e->getMessage();
            return false;
        }
    }

    public function markExpired(Reminder $reminder)
    { 
        try{
            if ($reminder instanceof Reminder) {
                $expired = $reminder->getExpired();
            }

            $expired = filter_var($expired, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            $idExpired = $reminder->getId();

            $query = "UPDATE note SET expired = ? WHERE id = ?";


            $stmt = $this->pdo->prepare($query);

            $stmt->bindParam(1, $expired, \PDO::PARAM_INT);
            $stmt->bindParam(2, $idExpired, \PDO::PARAM_INT);

            if ($stmt->execute()) {
                $response = array(
                    'success' => true,
                    'message' => 'Напоминание устарело'
            );
            } else {
                $response = array(
                    'success' => false,
                    'message' => 'Ошибка при обновлении напоминания: ' . $stmt->error
                );
            }
            echo json_encode($response);
        }
        catch (\PDOException $e) {
            echo "Ошибка при проверки напоминания: " . $e->getMessage();
            return false;
        }
    }
}