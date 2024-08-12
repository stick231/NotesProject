<?php
namespace Repository;

Use Entities\Database;
use Entities\AbstractNote;
use Entities\Note;
use Entities\Reminder;

class NoteRepository implements NoteRepositoryInterface{
    private $pdo;

    public function __construct(Database $database) {
        $this->pdo = $database->getConnection();
    }

    public function create(AbstractNote $abstractNote)
    {
        try{
            $query = "INSERT INTO note (title, content, time, reminder_time) VALUES (?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($query);

            $reminderTime = null;

            if ($abstractNote instanceof Reminder) {
                $reminderTime = $abstractNote->getReminderTime();
            }

            $params = array(
                $abstractNote->getTitle(),
                $abstractNote->getContent(),
                ('Y-m-d H:i:s'),
                $reminderTime
            );
            if ($stmt->execute($params)) {
                return true;
            } else {
                return false;
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
            if ($note->getSearch()) {
                $query = "SELECT * FROM note WHERE reminder_time IS NULL AND (title LIKE :search OR content LIKE :search OR time LIKE :search)";

                $stmt = $this->pdo->prepare($query);

                $searchParam = "%{$note->getSearch()}%";
                $stmt->bindParam(':search', $searchParam, \PDO::PARAM_STR);
            } else if (isset($this->id)) {
                $query = "SELECT * FROM note WHERE id = :id";

                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(':id', $note->getId(), \PDO::PARAM_INT);
            } else {
                $query = "SELECT * FROM note WHERE reminder_time IS NULL";
                $stmt = $this->pdo->prepare($query);
            }

        $stmt->execute();
        $notes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $response = $notes;

        echo json_encode($response);
        }
        catch (\PDOException $e) {
            echo "Ошибка при чтение заметки: " . $e->getMessage();
            return false;
        }
    }   

    public function readReminders(Reminder $reminder)
    {   
        try{
            $query = "SELECT * FROM note WHERE reminder_time IS NOT NULL";
            if (isset($this->search)) {
                $search = $reminder->getSearch();
                $query .= " AND (title LIKE '%$search%' OR content LIKE '%$search%' OR reminder_time LIKE '%$search%')";
            }

            $stmt = $this->pdo->prepare($query);

            $stmt->execute();


            if (!$stmt) {
                echo json_encode("Ошибка выполнения запроса: " . $this->pdo->error);
            } else {
                $notes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                $response = $notes;
                echo json_encode($response);
            }
        }
        catch (\PDOException $e) {
            echo "Ошибка при чтение напоминаний: " . $e->getMessage();
            return false;
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
            }
    
            $expired = ($reminderTimestamp > $currentTime) ? 0 : 1; 
    
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
                $response = array(
                    'success' => false,
                    'message' => 'Ошибка при обновлении устройства: '
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
        
            $query = "UPDATE note SET expired = ? WHERE id = ?";


            $stmt = $this->pdo->prepare($query);

            $stmt->bindParam(1, $expired, \PDO::PARAM_INT);
            $stmt->bindParam(2, $reminder->getId(), \PDO::PARAM_INT);

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