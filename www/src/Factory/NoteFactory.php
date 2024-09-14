<?php
namespace Factory;

use Entities\AbstractNote;
use Entities\Note;
use Entities\Reminder;

class NoteFactory implements NoteFactoryInterface{
    public function saveNote(string $type, $title, $content, $ReminderTime = null) :AbstractNote{
        switch ($type) {
            case 'reminder':
                return (new Reminder())
                    ->setTitle($title)
                    ->setContent($content)
                    ->setReminderTime($ReminderTime)
                    ->setType($type);
            case 'note':
                return (new Note())
                    ->setTitle($title)
                    ->setContent($content)
                    ->setType($type);
            default:
                throw new \InvalidArgumentException("Unknown note type: $type");
            }
    }
}