<?php

class Result
{
    public $userid;
    public $amount;
    public $course; 
    public $section;

    public $result;
    public $round;

    public function __construct($userid, $amount, $section, $result, $round) {
        $this->userid = $userid;
        $this->amount = $amount;
        $this->course = $course;
        $this->section = $section;

        $this->result = $result;
        $this->round = $round;
    }
  

}


