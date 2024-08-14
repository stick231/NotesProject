<?php
namespace Entities;

abstract class AbstractNote{
    protected $id;
    protected $search;
    protected $title;
    protected $content;
    private $created_at;
    protected $type;

    
    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getSearch()
    {
        return $this->search;
    }

    public function getTitle() 
    {
        return $this->title;
    }

    public function getContent() 
    {
        return $this->content;
    }

    public function getCreated_at()
    {
        return $this->created_at;
    }

    public function setSearch($search)
    {
        $new = clone $this;
        $new->search = $search;
        return $new;
    }

    public function setTitle($title) 
    {
        $new = clone $this;
        $new->title = $title;
        return $new;
    }

    public function setContent($content) 
    {
        $new = clone $this;
        $new->content = $content;
        return $new;
    }
    public function setType(string $type)
    {
        $new = clone $this;
        $new->type = $type;
        return $new;
    }

    abstract public function getType(): string;
}