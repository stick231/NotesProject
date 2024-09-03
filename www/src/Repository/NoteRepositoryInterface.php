<?php
namespace Repository;

use Entities\AbstractNote;
use Entities\Note;
use Entities\Reminder;

interface NoteRepositoryInterface {
    public function create(AbstractNote $abstractNote); 
    public function readNote(Note $note); 
    public function delete(AbstractNote $abstractNote); 
    public function readReminders(Reminder $reminder); 
    public function update(AbstractNote $abstractNote); 
    public function markExpired(Reminder $reminder) ; 
}