<?php
namespace Entities;

class User {
    private $username;
    private $email;
    private $password;

    public function getUsername() 
    {
        return $this->username;
    }

    public function getEmail() 
    {
        return $this->email;
    }

    public function getPassword() 
    {
        return $this->password;
    }

    public function setUsername($username)
    {
        $new = clone $this;
        $new->username = $username;
        return $new;
    }

    public function setEmail($email)
    {
        $new = clone $this;
        $new->email = $email;
        return $new;
    }

    public function setPassword($password)
    {
        $new = clone $this;
        $password = password_hash($password, PASSWORD_DEFAULT); 
        $new->password = $password;
        return $new;
    }
}