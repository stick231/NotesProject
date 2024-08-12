<?php
namespace Factory;

use Entities\AbstractNote;

interface NoteFactoryInterface{
    public function saveNote(string $type, $id, $search = '',  $title, $content, $ReminderTime = null): AbstractNote;
}