<?php

class CourseEnrolledDAO {

    /*
:userid, :course, :section, :day, :start, :end, 
:exam_date, :exam_start, :exam_end
    */
    public function add($course_enrolled) {
        $sql = 'INSERT IGNORE into course_enrolled(userid, course, section, day, start, end, exam_date, exam_start, exam_end) values (:userid, :course, :section, :day, :start, :end, :exam_date, :exam_start, :exam_end)';
        
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $course->course, PDO::PARAM_STR);
        $stmt->bindParam(':course', $course->school, PDO::PARAM_STR);
        $stmt->bindParam(':section', $course->title, PDO::PARAM_STR);
        $stmt->bindParam(':day', $course->description, PDO::PARAM_STR);
        $stmt->bindParam(':start', $course->exam_date, PDO::PARAM_STR);
        $stmt->bindParam(':end', $course->exam_start, PDO::PARAM_STR);
        $stmt->bindParam(':exam_date', $course->exam_end, PDO::PARAM_STR);
        $stmt->bindParam(':exam_start', $course->exam_end, PDO::PARAM_STR);
        $stmt->bindParam(':exam_end', $course->exam_end, PDO::PARAM_STR);


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
