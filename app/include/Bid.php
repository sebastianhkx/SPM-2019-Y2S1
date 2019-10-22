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
    public function getUserid(){
        return $this->userid;
    }
    public function getCourse(){
        return $this->course;
    }
    public function getSection(){
        return $this->section;
    }
    public function getAmountJSON(){
        return number_format($this->amount,1);
}
}

?>