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
    public function getCourse(){
        return $this->course;
    }

    public function getSection(){
        return $this->section;
    }

    public function getDay(){
        $arr = ["","Monday","Tuesday","Wednesday","Thursday","Friday"];
        return $arr[$this->day];
    }

    public function getStart(){
        return $this->start;
    }
    
    public function getStartJSON(){
        $var1=$this->start;
        $fstart=strtotime($var1);
        $var2=date('Hi',$fstart);
        if($var1[0]=="0"){
            $start=substr($var2,1,4);
        }
        else{
            $start=substr($var2,0,5);
        }
        return $start;
    }


    public function getExamEndJSON(){
        $var1=$this->exam_end;
        $fexam_end=strtotime($var1);
        $var2=date('Hi',$fexam_end);
        if($var2[0]=="0"){
            $exam_end=substr($var2,1,4);
        }
        else{
            $exam_end=substr($var2,0,5);
        }
        return $exam_end;
    }


    public function getEnd(){
        return $this->end;
    }

    public function getEndJSON(){
        $var1=$this->end;
        $fend=strtotime($var1);
        $var2=date('Hi',$fend);
        if($var1[0]=="0"){
            $end=substr($var2,1,4);
        }
        else{
            $end=substr($var2,0,5);
        }
        return $end;
    }

    public function getInstructor(){
        return $this->instructor;
    }

    public function getVenue(){
        return $this->venue;
    }

    public function getSize(){
        return $this->size;
    }
    
}

?>