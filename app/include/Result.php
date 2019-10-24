<?php

class Result
{
    public $userid;
    public $amount;
    public $course; 
    public $section;

    public $result;
    public $round_num;

    public function __construct($userid, $amount, $course, $section, $result, $round_num) {
        $this->userid = $userid;
        $this->amount = $amount;
        $this->course = $course;
        $this->section = $section;

        $this->result = $result;
        $this->round_num = $round_num;
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
    public function getResult(){
        if ($this->result=="success"){
            return "in";
        }
        else{
            return "out";
        }
    }

  

}


