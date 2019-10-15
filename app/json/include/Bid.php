<?php

class Bid {
    public $userid;
    public $amount;
    public $course;    
    public $section;
    
    public function __construct($userid, $amount, $course, $section) {
        $this->userid = $userid;
        $this->amount = $amount;
        $this->course = $course;
        $this->section = $section;
    }
}

?>