<?php

class PrerequisiteDAO{

    public function retrieveAll(){
        $sql = 'SELECT * FROM prerequisite';
        
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch()) {
            $result[] = new Prerequisite($row['course'], $row['prerequisite']);
        }

        $stmt = null;
        $conn = null; 
                 
        return $result;
    }

    public function deleteAll(){
        $sql = 'TRUNCATE TABLE prerequisite';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $count = $stmt->rowCount();

        $stmt = null;
        $conn = null; 
    }

    public function add($prerequisite){
        /*
        takes in prerequisite object
        returns array of error strings if validation failed, returns null of validation passed
        */
        $errors = [];
        $courseDAO = new CourseDAO;
        if ($courseDAO->retrieveByCourseId($prerequisite->course)==null){
            $errors[] = "invalid course";
        }
        if ($courseDAO->retrieveByCourseId($prerequisite->prerequisite)==null){
            $errors[] = "invalid prerequisite";
        }
        
        if (!empty($errors)){
            return $errors;
        }

        $sql = 'INSERT IGNORE into prerequisite(course, prerequisite) values (:course, :prerequisite)';
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();


        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $prerequisite->course, PDO::PARAM_STR);
        $stmt->bindParam(':prerequisite', $prerequisite->prerequisite, PDO::PARAM_STR);

        $stmt->execute();
        
        $stmt = null;
        $conn = null; 
    }

    public function retrievePrerequisite($courseid){
        // returns array of prerequiste courseid for input courseid
        $sql = 'SELECT * FROM prerequisite where course=:courseid';
        
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(":courseid", $courseid, PDO::PARAM_STR);
        $stmt->execute();
        
        $result = null;

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row['prerequisite'];
        }

        $stmt = null;
        $conn = null; 
        return $result;
    }

}

?>