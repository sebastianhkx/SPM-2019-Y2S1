<?php

class CourseEnrolledDAO {

    /*
:userid, :course, :section, :day, :start, :end, 
:exam_date, :exam_start, :exam_end
    */
    public function add($course_enrolled) {
        $sql = "INSERT IGNORE INTO course_enrolled(userid, course, section, day, start, end, exam_date, exam_start, exam_end) values(:userid, :course, :section, :day, :start, :end, :exam_date, :exam_start, :exam_end)";
        
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $course_enrolled->userid, PDO::PARAM_STR);
        $stmt->bindParam(':course', $course_enrolled->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $course_enrolled->section, PDO::PARAM_STR);
        $stmt->bindParam(':day', $course_enrolled->day, PDO::PARAM_INT);
        $stmt->bindParam(':start', $course_enrolled->start, PDO::PARAM_STR);
        $stmt->bindParam(':end', $course_enrolled->userid, PDO::PARAM_STR);
        $stmt->bindParam(':exam_date', $course_enrolled->exam_date, PDO::PARAM_STR);
        $stmt->bindParam(':exam_start', $course_enrolled->exam_start, PDO::PARAM_STR);
        $stmt->bindParam(':exam_end', $course_enrolled->exam_end, PDO::PARAM_STR);


        $stmt->execute();
        
        $stmt = null;
        $conn = null; 


    }
    public function deleteAll(){
        $sql = 'TRUNCATE TABLE course_enrolled';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $count = $stmt->rowCount();
    }
}
