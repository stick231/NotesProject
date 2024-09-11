<?php
namespace Entities;

class Reminder extends AbstractNote{
    private $expired;
    private $ReminderTime;
    
    public function getType(): string
    {
        return "reminder";    
    }

    public function getReminderTime()
    {
        return $this->ReminderTime;
    }

    public function getExpired()
    {
        return $this->expired;
    }

    public function setReminderTime($ReminderTime)
    {
        $new = clone $this;
        $new->ReminderTime = $ReminderTime;
        return $new;
    }
    
    public function setExpired($expired)
    {
        $new = clone $this;
        $new->expired = $expired;
        return $new;
    }
}