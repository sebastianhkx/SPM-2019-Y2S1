<?php

class Section {
    public $course;
    public $section;
    public $day;    
    public $start_time;
    public $end_time;
    public $instructor;
    public $venue;
    public $size;
    
    public function __construct($course, $section, $day, $start_time, $end_time, $instructor, $venue, $size) {
        $this->course = $course;
        $this->section = $section;
        $this->day = $day;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->instructor = $instructor;
        $this->venue = $venue;
        $this->size = $size;
    }
    
}

?>