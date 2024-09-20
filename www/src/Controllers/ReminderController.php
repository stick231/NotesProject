<?php
namespace Controllers;

use Entities\Reminder;
use Repository\NoteRepository;
use Factory\NoteFactory;

class ReminderController{
    private $noteRepository;
    private $noteFactory;

    public function __construct(NoteRepository $noteRepository, NoteFactory $noteFactory = null) {
        $this->noteRepository = $noteRepository;
        $this->noteFactory = $noteFactory;
    }

    public static function getActionMethodsReminder() {
        return [
            'createReminder' => 'createReminder',
            'deleteReminder' => 'deleteReminder',
            'updateReminder' => 'updateReminder',
            'markExpired' => 'markExpired'
        ];
    }

    public function createReminder() 
    {
        if (isset($_POST["title"]) && isset($_POST['content']) && isset($_POST['createReminder'])) {
            $reminder = $this->noteFactory->saveNote('reminder', $_POST["title"], $_POST['content'], $_POST['reminder_time']);
            
            $this->noteRepository->create($reminder);
            exit;
        } else {
            echo json_encode(['error' => 'Недостаточно данных для создания заметки.']);
            exit;
        }
    }
    public function readReminder()
    {
        if (isset($_POST['search'])) {
            $reminderWithSearch = (new Reminder())->setSearch($_POST['search']);
            echo $this->noteRepository->readReminders($reminderWithSearch);
            exit;    
        }
        $reminder = new Reminder();
        echo $this->noteRepository->readReminders($reminder);
        exit;
    }
    public function deleteReminder()
    {
        if (isset($_POST['deleteReminder']) && isset($_POST["id"])) {
            $reminderWithId = (new Reminder())->setId($_POST['id']);
            echo $this->noteRepository->delete($reminderWithId);
            exit;
        }
    }
    public function updateReminder()
    {
        if(isset($_POST['updateReminder']) && isset($_POST["title"]) && isset($_POST['content'])) {
            $reminderUpdate = null;
        
            if(isset($_POST['reminder_time']) && $_POST['reminder_time'] !== null ) {
                $reminderUpdate = $this->noteFactory->saveNote('reminder', $_POST['title'], $_POST['content'], $_POST["reminder_time"]);
                $reminderUpdate = $reminderUpdate->setId($_POST['id']);
            }
            $this->noteRepository->update($reminderUpdate);
            exit;
        }
    }
    public function markExpired()
    {
        if(isset($_POST['markExpired']) && isset($_POST['id'])){
            $reminderCheck = (new Reminder())->setExpired($_POST['markExpired'])->setId($_POST['id']);
            $this->noteRepository->markExpired($reminderCheck);
            exit;
        }
    }
}