<?php
namespace Factory;

use Entities\AbstractNote;

interface NoteFactoryInterface{
    public function saveNote(string $type, $title, $content, $ReminderTime = null) :AbstractNote;
}