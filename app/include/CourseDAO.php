<?php

class CourseDAO {

    public  function retrieveAll() {
        $sql = 'SELECT * FROM course ORDER BY `course`';
        
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch()) {
            $result[] = new Course($row['course'], $row['school'], $row['title'], $row['description'], $row['exam_date'], $row['exam_start'], $row['exam_end']);
        }

        $stmt = null;
        $conn = null; 
                 
        return $result;
    }

    public function retrieveBySchool($school){
        //step 1 
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();


        // Step 2 - Write & Prepare SQL Query (take care of Param Binding if necessary)
        $sql = 'SELECT * FROM course where school = :school ORDER BY course';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':school',$school,PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $stmt->execute();
        // Step 3 - Execute SQL Query
        $arr = [];

        while($row = $stmt->fetch()){
            $arr[] = new Course($row['course'], $row['school'], $row['title'], $row['description'], $row['exam_date'], $row['exam_start'], $row['exam_end']);

            }

        $stmt = null;
        $conn = null; 
                 
        return $arr;
    }
    public function deleteAll(){
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $sql = 'SET foreign_key_checks = 0';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $sql = 'TRUNCATE TABLE course';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $count = $stmt->rowCount();

        $sql = 'SET foreign_key_checks = 1';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $stmt = null;
        $conn = null; 
    }

    public function add($course){
        $sql = 'INSERT IGNORE into course(course, school, title, description, exam_date, exam_start, exam_end) values (:course, :school, :title, :description, :exam_date, :exam_start, :exam_end)';
        
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $course->course, PDO::PARAM_STR);
        $stmt->bindParam(':school', $course->school, PDO::PARAM_STR);
        $stmt->bindParam(':title', $course->title, PDO::PARAM_STR);
        $stmt->bindParam(':description', $course->description, PDO::PARAM_STR);
        $stmt->bindParam(':exam_date', $course->exam_date, PDO::PARAM_STR);
        $stmt->bindParam(':exam_start', $course->exam_start, PDO::PARAM_STR);
        $stmt->bindParam(':exam_end', $course->exam_end, PDO::PARAM_STR);

        $stmt->execute();
        
        $stmt = null;
        $conn = null; 
    }

    public function retrieveByCourseId($courseId){
        //returns course obj if courseid exists, returns null if doesn't
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();


        $sql = 'SELECT * FROM course where course = :courseId';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':courseId',$courseId,PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $stmt->execute();
        $result = null;

        while($row = $stmt->fetch()){
            $result = new Course($row['course'], $row['school'], $row['title'], $row['description'], $row['exam_date'], $row['exam_start'], $row['exam_end']);
        }

        $stmt = null;
        $conn = null; 
                 
        return $result;

    }
}