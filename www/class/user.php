<?php
date_default_timezone_set('Europe/Moscow');

class User
{   
    private $conn;
    private $id;
    private $title;
    private $content;
    private $time;
    private $reminderTime;
    private $expired;
    private $search;

    function __construct($db)
    {
        $this->conn = $db;
        $this->time = date('Y-m-d H:i:s');
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setTitle($title)
    {   
        $this->title = $title;
    }
    public function setContent($content)
    {
        $this->content = $content;
    }
    public function setReminderTime($reminderTime)
    {
        $this->reminderTime = $reminderTime;
    }
    public function setSearch($search)
    {
        $this->search = $search;
    }
    public function setExpired($expired)
    {
        $this->expired = $expired;
    }

    public function createNote()
    {

        $query = "INSERT INTO note (title, content, time, reminder_time) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);

        $params = array(
            $this->title, 
            $this->content,
            $this->time, 
            $this->reminderTime
        );
        if($stmt->execute($params)){
            return true;
        } else {
            return false;
        }
    }
    public function readNote()
{
    if ($this->search) {
        $query = "SELECT * FROM note WHERE reminder_time IS NULL AND (title LIKE :search OR content LIKE :search OR time LIKE :search)";

        $stmt = $this->conn->prepare($query);

        $searchParam = "%{$this->search}%";
        $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
    } else if (isset($this->id)) {
        $query = "SELECT * FROM note WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
    } else {
        $query = "SELECT * FROM note WHERE reminder_time IS NULL";
        $stmt = $this->conn->prepare($query);
    }

    $stmt->execute();
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = $notes;

    echo json_encode($response);
}
    public function readReminders()
    {
        $query = "SELECT * FROM note WHERE reminder_time IS NOT NULL";
        if(isset($this->search)){
            $search = $this->search;
            $query .= " AND (title LIKE '%$search%' OR content LIKE '%$search%' OR reminder_time LIKE '%$search%')";
        }
    
        $stmt = $this->conn->prepare($query);

        $stmt->execute();

    
        if (!$stmt) {
            echo json_encode("Ошибка выполнения запроса: " . $this->conn->error);
        }
        else{
            $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response = $notes;
            echo json_encode($response);
        }
    }
    public function delete()
    {
        $query = "DELETE FROM note WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id, PDO::PARAM_INT);
        if ($stmt->execute()) 
        {
            $response = array(
                'success' => true,
                'message' => 'Устройство успешно удалено'
            );
        } else 
        {
            $response = array(
                'success' => false,
                'message' => 'Ошибка при удалении устройства: ' . $stmt->error
            );
        }

        echo json_encode($response);
    }
    public function update()
    {
        $currentTime = strtotime(date('Y-m-d H:i:s'));
        $reminderTimestamp = strtotime($this->time);
    
        $expired = ($reminderTimestamp > $currentTime) ? 0 : 1; 
    
        $query = "UPDATE note SET title = ?, content = ?, last_update = ?, reminder_time = ?, expired = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $params = array($this->title,
            $this->content,
            $this->time, 
            $this->reminderTime, 
            $expired, 
            $this->id
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
    public function expiredReminders()
    { 
        if ($this->expired) {
            $query = "UPDATE note SET expired = TRUE WHERE id = ?";
        } else {
            $query = "UPDATE note SET expired = FALSE WHERE id = ?";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id, PDO::PARAM_INT);

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
}
?>