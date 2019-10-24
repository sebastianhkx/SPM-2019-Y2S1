<?php

class CourseEnrolled {
    public $userid;
    public $course;
    public $section;
    public $amount;

    public $day;
    public $start;
    public $end;

    public $exam_date;
    public $exam_start;
    public $exam_end;

    
    public function __construct($userid, $course, $section, $amount, $day, $start, $end, $exam_date, $exam_start, $exam_end) {
        $this->userid = $userid;
        $this->course = $course;
        $this->section = $section;
        $this->amount = $amount;

        $this->day = $day;
        $this->start = $start;
        $this->end = $end;

        $this->exam_date = $exam_date;
        $this->exam_start = $exam_start;
        $this->exam_end = $exam_end;
    }


    public function getUserid(){
        return $this->userid;
    }

    public function getAmountJSON(){
        return number_format($this->amount,1);
}
    
}

?>