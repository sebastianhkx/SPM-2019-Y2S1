<?php

class CourseCompleted {
    public $userid;
    public $code;
    
    public function __construct($userid, $code) {
        $this->userid = $userid;
        $this->code = $code;
    }
    
}

?>