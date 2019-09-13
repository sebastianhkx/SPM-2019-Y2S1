<?php

class Course {
    public $course;
    public $school;
    public $title;    
    public $description;
    public $exam_date;
    public $exam_start_time;
    public $exam_end_time;
    
    public function __construct($course, $school, $title, $description, $exam_date, $exam_start_time, $exam_end_time) {
        $this->course = $course;
        $this->school = $school;
        $this->title = $title;
        $this->description = $description;
        $this->exam_date = $exam_date;
        $this->exam_start_time = $exam_start_time;
        $this->exam_end_time = $exam_end_time;
    }
    
}

?>