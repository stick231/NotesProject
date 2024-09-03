<?php
namespace Entities;

class Note extends AbstractNote{
    public function getType(): string
    {
        return "note";
    }
}