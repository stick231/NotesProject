<?php
namespace Factory;

use Entities\AbstractNote;
use Entities\Note;
use Entities\Reminder;

class NoteFactory implements NoteFactoryInterface{
    public function saveNote(string $type, $title, $content, $ReminderTime = null, $search = ''): AbstractNote {
        switch ($type) {
            case 'reminder':
                return (new Reminder())
                    ->setTitle($title)
                    ->setContent($content)
                    ->setReminderTime($ReminderTime)
                    ->setType($type)
                    ->setSearch($search);
            case 'note':
                return (new Note())
                    ->setTitle($title)
                    ->setContent($content)
                    ->setType($type)
                    ->setSearch($search);
            default:
                throw new \InvalidArgumentException("Unknown note type: $type");
            }
    }
}