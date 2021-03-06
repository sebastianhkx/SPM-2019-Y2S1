<?php

class SectionDAO {

    public  function retrieveAll() {
        $sql = 'SELECT * FROM section ORDER BY `course`';
        
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch()) {
            $result[] = new Section($row['course'], $row['section'], $row['day'], $row['start'], $row['end'], $row['instructor'], $row['venue'], (int)$row['size']);
        }
        
        $stmt = null;
        $conn = null; 
                 
        return $result;
    }

    public  function retrievebyCourse($course) {
        $sql = 'SELECT * FROM section WHERE course=:course';
        
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch()) {
            $result[] = new Section($row['course'], $row['section'], $row['day'], $row['start'], $row['end'], $row['instructor'], $row['venue'], (int)$row['size']);
        }
        
        $stmt = null;
        $conn = null; 
                 
        return $result;
    }

    public  function retrievebyCourseSection($course, $section) {
        $sql = 'SELECT * FROM section WHERE course=:course and section=:section';
        
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = null;

        while($row = $stmt->fetch()) {
            $result = new Section($row['course'], $row['section'], $row['day'], $row['start'], $row['end'], $row['instructor'], $row['venue'], (int)$row['size']);
        }
        
        $stmt = null;
        $conn = null; 
                 
        return $result;
    }

    public function deleteAll(){

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $sql = 'SET foreign_key_checks = 0';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $sql = 'TRUNCATE TABLE section';

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $count = $stmt->rowCount();

        $sql = 'SET foreign_key_checks = 1';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        $stmt = null;
        $conn = null; 
    }

    public function add($section){
    //takes in section object
        $errors=[];

        $course=$section->course;
        $courseDAO= new CourseDAO;
        
        if ($courseDAO->retrieveByCourseId($section->course)==null){
            $errors[] = "invalid course";
        }
        else{
            $section_name=$section->section;
            $section_number=substr($section_name,1);
            // var_dump($section);
            if($section_name[0] != 'S' || !is_numeric($section_number) || $section_number<1  || $section_number>99 || substr($section_number,0,1)=='0'){
                $errors[]='invalid section';
            }
        }  

        if( $section->day<1 || $section->day>7 ){
            $errors[]= 'invalid day';
        }

        if( $section->start!=date("G:i",strtotime($section->start)) ){
            $errors[]='invalid start';
        }
        if($section->end!=date("G:i",strtotime($section->end))  || strtotime($section->end)<=strtotime($section->start)){
            $errors[]='invalid end';
        }
        else{
            $start_time=explode(":",date("G:i",strtotime($section->start)));
            $end_time=explode(":",date("G:i",strtotime($section->end)));
            if($start_time[0]*60+$start_time[1]>$start_time[0]*60+$start_time[1] ){
                $errors[]='invalid end';
            }
        }
    
        if(strlen($section->instructor)>100){
            $errors[]='invalid instructor';
        }
        if(strlen($section->venue)>100){
            $errors[]='invalid venue';
        }
        if( ! ($section->size>0 && is_numeric($section->size))){
            $errors[]='invalid size';
        }
        if (!empty($errors)){
            return $errors; 
        }

        $sql = 'INSERT IGNORE into section(course, section, day, start, end, instructor, venue, size) values (:course, :section, :day, :start, :end, :instructor, :venue, :size)';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $section->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section->section, PDO::PARAM_STR);
        $stmt->bindParam(':day', $section->day, PDO::PARAM_INT);
        $stmt->bindParam(':start', $section->start, PDO::PARAM_STR);
        $stmt->bindParam(':end', $section->end, PDO::PARAM_STR);
        $stmt->bindParam(':instructor', $section->instructor, PDO::PARAM_STR);
        $stmt->bindParam(':venue', $section->venue, PDO::PARAM_STR);
        $stmt->bindParam(':size', $section->size, PDO::PARAM_INT);

        $stmt->execute();
        
        $stmt = null;
        $conn = null; 
    }
    public function retrieveBySection($bid){
        //This takes in a bid object and return section object that existed, otherwise return null
        //step 1 
        $connMgr = new ConnectionManager();
        $pdo = $connMgr->getConnection();


        // Step 2 - Write & Prepare SQL Query (take care of Param Binding if necessary)
        $sql = 'SELECT * FROM section WHERE section=:section AND course=:course';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':section',$bid->section,PDO::PARAM_STR);
        $stmt->bindParam(':course',$bid->course,PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $stmt->execute();
        // Step 3 - Execute SQL Query
        $result = null;

        while($row = $stmt->fetch()){
            $result = new Section($row['course'], $row['section'], $row['day'], $row['start'], $row['end'], $row['instructor'], $row['venue'], $row['size']);

        }

        $stmt = null;
        $conn = null; 
                 
        return $result;
    }

    public function retrieveSection($course, $section){
        //This takes in a bid object and return section object that existed, otherwise return null
        //step 1 
        $connMgr = new ConnectionManager();
        $pdo = $connMgr->getConnection();


        // Step 2 - Write & Prepare SQL Query (take care of Param Binding if necessary)
        $sql = 'SELECT * FROM section WHERE section=:section AND course=:course';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':section',$section,PDO::PARAM_STR);
        $stmt->bindParam(':course',$course,PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $stmt->execute();
        // Step 3 - Execute SQL Query
        $result = null;

        while($row = $stmt->fetch()){
            $result = new Section($row['course'], $row['section'], $row['day'], $row['start'], $row['end'], $row['instructor'], $row['venue'], $row['size']);

        }

        $stmt = null;
        $conn = null; 
                 
        return $result;
    }

}