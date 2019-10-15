<?php

class Admin {
    private $userid;
    private $password;

    function __construct($userid, $password) {
        $this->userid = $userid;
        $this->password = $password;
    }
    
    public function authenticate($enteredPwd) {
        return password_verify($enteredPwd, $this->password);
    }




   
}
