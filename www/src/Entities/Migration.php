<?php

namespace Entities;

class Migration{
    private $id;
    private $status;
    private $query;
    
    public function getId()
    {
        return $this->id;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function getQuery()
    {
        return $this->query;
    }
    public function withId($id)
    {
        $new = clone $this;
        $new->id = $id;
        return $new; 
    }
    public function withStatus($status)
    {  
        $new = clone $this;
        $new->status = $status;
        return $new; 
    }
    public function withQuery($query)
    {
        $new = clone $this;
        $new->query = $query;
        return $new;
    }
}