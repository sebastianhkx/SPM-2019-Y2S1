<?php

class Course {
    public $course;
    public $school;
    public $title;    
    public $description;
    public $exam_date;
    public $exam_start;
    public $exam_end;
    
    public function __construct($course, $school, $title, $description, $exam_date, $exam_start, $exam_end) {
        $this->course = $course;
        $this->school = $school;
        $this->title = $title;
        $this->description = $description;
        $this->exam_date = $exam_date;
        $this->exam_start = $exam_start;
        $this->exam_end = $exam_end;
    }
    public function getCourse(){
        return $this->course;
    }

    public function getSchool(){
        return $this->school;
    }

    public function getTitle(){
        return $this->title;
    }

    public function getDescription(){
        return $this->description;
    }

    public function getExamDate(){
        return $this->exam_date;
    }

    public function getExamDateJSON(){
        $date=date_create($this->exam_date);
        $fdate=date_format($date,"Ymd");
        return $fdate;
    }

    public function getExamStart(){
        return $this->exam_start;
    }

    public function getExamStartJSON(){
        $var1=$this->exam_start;
        $fexam_start=strtotime($var1);
        $var2=date('Hi',$fexam_start);
        if($var2[0]=="0"){
            $exam_start=substr($var2,1,4);
        }
        else{
            $exam_start=substr($var2,0,5);
        }
        return $exam_start;
    }

    public function getExamEnd(){
        return $this->exam_start;
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
}

?>