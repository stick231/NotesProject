<?php
namespace Factory;

use Entities\AbstractNote;
use Entities\Note;
use Entities\Reminder;

class NoteFactory implements NoteFactoryInterface{
    public function saveNote(string $type, $id, $search = '',  $title, $content, $ReminderTime = null): AbstractNote {
        switch ($type) {
            case 'reminder':
                return (new Reminder())
                ->setId($id)
                ->setSearch($search)
                ->setTitle($title)
                ->setContent($content)
                ->setReminderTime($ReminderTime);
                case 'note':
                    return (new Note())
                        ->setId($id)
                        ->setSearch($search)
                        ->setTitle($title)
                        ->setContent($content);
                default:
                    throw new \InvalidArgumentException("Unknown note type: $type");
            }
    }
}