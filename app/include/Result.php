<?php

class Result
{
    public $userid;
    public $amount;
    public $course; 
    public $section;

    public $outcome;
    public $round;

    public function __construct($userid, $amount, $course, $section, $outcome, $round) {
        $this->userid = $userid;
        $this->amount = $amount;
        $this->course = $course;
        $this->section = $section;

        $this->outcome = $outcome;
        $this->round = $round;
    }
  

}


