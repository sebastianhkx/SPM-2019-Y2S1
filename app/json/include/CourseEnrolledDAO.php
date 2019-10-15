<?php

class CourseEnrolledDAO {

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

    public function retrieveByUserid($userid){
        $sql = "SELECT * from course_enrolled where userid = :userid";

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->execute();

        $result = [];

        while($row = $stmt->fetch()){
            $result[] = new CourseEnrolled($row['userid'], $row['course'], $row['section'], $row['day'], $row['start'], $row['end'], $row['exam_date'], $row['exam_start'], $row['exam_end']);
        }
        return $result;
    }

    public function retrieveByUseridCourse($userid, $course){
        $sql = "SELECT * from course_enrolled where userid = :userid and course = :course";

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);

        $stmt->execute();

        $result = null;

        while($row = $stmt->fetch()){
            $result = new CourseEnrolled($row['userid'], $row['course'], $row['section'], $row['day'], $row['start'], $row['end'], $row['exam_date'], $row['exam_start'], $row['exam_end']);
        }
        return $result;
    }

    public function delete($courseEnrolled){
        //takes in a CourseEnrolled object
        $sql = 'DELETE from course_enrolled where userid = :userid and course = :course and section = :section';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $courseEnrolled->userid, PDO::PARAM_STR);
        $stmt->bindParam(':course', $courseEnrolled->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $courseEnrolled->section, PDO::PARAM_STR);
        
        $isDeleteOk = FALSE;
        if ($stmt->execute()) {
            $isDeleteOk = TRUE;
        }

        $stmt = null;
        $conn = null; 

        return $isDeleteOk;
    }

    ## truncate tables when bootstraping
    public function deleteAll(){
        $sql = 'TRUNCATE TABLE course_enrolled';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $count = $stmt->rowCount();
    }

    public function retrieveByCourseSection($courseSection){
        //this takes in an array [course, section] and returns array of courseEnrolledObjs
        $sql = "SELECT * from course_enrolled where course = :course and section = :section";

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $courseSection[0], PDO::PARAM_STR);
        $stmt->bindParam(':section', $courseSection[1], PDO::PARAM_STR);

        $stmt->execute();

        $result = null;

        while($row = $stmt->fetch()){
            $result[] = new CourseEnrolled($row['userid'], $row['course'], $row['section'], $row['day'], $row['start'], $row['end'], $row['exam_date'], $row['exam_start'], $row['exam_end']);
        }
        return $result;
    }


}