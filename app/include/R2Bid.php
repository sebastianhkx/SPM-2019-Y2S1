<?php

class R2Bid {
    public $course;
    public $section;
    public $min_amount;    
    public $vacancy;
    
    public function __construct($course, $section, $min_amount, $vacancy) {
        $this->course = $course;
        $this->section = $section;
        $this->min_amount = $min_amount;
        $this->vacancy = $vacancy;
    }
}

?>