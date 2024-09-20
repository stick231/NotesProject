<?php

namespace Controllers;

use Entities\Note;
use Repository\NoteRepository;
use Factory\NoteFactory;

class NoteController{
    private $noteRepository;
    private $noteFactory;

    public function __construct(NoteRepository $noteRepository, NoteFactory $noteFactory = null) {
        $this->noteRepository = $noteRepository;
        $this->noteFactory = $noteFactory;
    }

    public static function getActionMethodsNote() {
        return [
            'createNote' => 'createNote',
            'deleteNote' => 'deleteNote',
            'updateNote' => 'updateNote'
        ];
    }

    public function createNote() {
        if (isset($_POST["title"]) && isset($_POST['content']) && isset($_POST['createNote'])) {
            $note = $this->noteFactory->saveNote('note', $_POST["title"], $_POST['content']);
            
            $this->noteRepository->create($note);
            exit;
        } else {
            echo json_encode(['error' => 'Недостаточно данных для создания заметки.']);
            exit;
        }
    }
    
    public function readNote()
    {
        if (isset($_POST['search'])) {
            $noteWithSearch = (new Note())->setSearch($_POST['search']);
            echo $this->noteRepository->readNote($noteWithSearch);
            exit;    
        }
        $note = new Note();
        echo $this->noteRepository->readNote($note);
        exit;
    }

    public function deleteNote()
    {
        if (isset($_POST['deleteNote']) && isset($_POST["id"])) {
            $noteWithId = (new Note())->setId($_POST['id']);
            $this->noteRepository->delete($noteWithId);
            exit;
        }
    }

    public function updateNote()
    {
        if(isset($_POST['updateNote']) && isset($_POST["title"]) && isset($_POST['content']) && isset($_POST['id'])) {
            $noteUpdate = $this->noteFactory->saveNote('note', $_POST['title'], $_POST['content']);
            $noteUpdate = $noteUpdate->setId($_POST['id']);
            
            $this->noteRepository->update($noteUpdate);
            exit;
        }
    }
}