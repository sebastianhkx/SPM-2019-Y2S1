<?php

class Section {
    public $course;
    public $section;
    public $day;    
    public $start;
    public $end;
    public $instructor;
    public $venue;
    public $size;
    
    public function __construct($course, $section, $day, $start, $end, $instructor, $venue, $size) {
        $this->course = $course;
        $this->section = $section;
        $this->day = $day;
        $this->start = $start;
        $this->end = $end;
        $this->instructor = $instructor;
        $this->venue = $venue;
        $this->size = $size;
    }
    
}

?>