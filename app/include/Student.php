<?php

class Student {
    public $userid;
    public $password;
    public $name;    
    public $school;
    public $edollar;
    
    public function __construct($userid, $password, $name, $school, $edollar) {
        $this->userid = $userid;
        $this->password = $password;
        $this->name = $name;
        $this->school = $school;
        $this->edollar = $edollar;
    }

    public function getUserid(){
        return $this->userid;
    }

    public function getPassword(){
        return $this->password;
    }
    public function getName(){
        return $this->name;
    }
    public function getSchool(){
        return $this->school;
    }
    public function getEdollar(){
        return $this->edollar;
    }

    public function getEdollarJSON(){
        return number_format($this->edollar,2);
}

    ## Hashed pw auth
    // public function authenticate($enteredPwd) {
    //     return password_verify($enteredPwd, $this->password);
    // }

    public function authenticate($enteredPwd) {
        if ($enteredPwd === $this->password) {
            return TRUE;
        }
    }
}

?>