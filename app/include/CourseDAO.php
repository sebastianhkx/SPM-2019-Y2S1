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
}