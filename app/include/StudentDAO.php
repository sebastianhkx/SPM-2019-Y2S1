<?php

class StudentDAO {

    public function retrieveAll(){
        $sql = 'SELECT userid, password, name, school, edollar FROM student';
        
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch()) {
            $result[] = new Student($row['userid'], $row['password'], $row['name'], $row['school'],['edollar']);
        }

        $stmt = null;
        $conn = null; 
                 
        return $result;
    }

    public function retrieve($userid){
        $sql = 'SELECT userid, password, name, school, edollar FROM student where userid=:userid';
        
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(":userid", $userid, PDO::PARAM_STR);
        $stmt->execute();

        

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return new Student($row['userid'], $row['password'], $row['name'], $row['school'],['edollar']);
        }

        $stmt = null;
        $conn = null; 
                 
        
    }

    public function deleteAll(){

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $sql = 'SET foreign_key_checks = 0';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $sql = 'TRUNCATE table student';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $count = $stmt->rowCount();

        $sql = 'SET foreign_key_checks = 1';
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        
        $stmt = null;
        $conn = null; 
    }

    public function add($student){
        /*
        takes in student obj and adds it to the database
        returns array of errors if validation fails, returns null if validation passed
        */
        
        //validation
        $errors = [];

        if (strlen($student->userid)>128){
            $errors[] = "invalid userid";
        }

        if ($this->retrieve($student->userid)!=null){
            $errors[] = "duplicate userid";
        }

        $edollar_array = explode('.',$student->edollar);
        if (isset($edollar_array[1])){
            $decimal_place = $edollar_array[1];
        }
        else{
            $decimal_place = 0;
        }
        if ($student->edollar<0 || strlen($decimal_place)>2){
            //2nd condition checks for edollars decimal place
            $errors[] = "invalid e-dollar";
        } 

        if (strlen($student->password)>128){
            $errors[] = "invalid password";
        }

        if (strlen($student->name)>100){
            $errors[] = "invalid name";
        }

        if (!empty($errors)){
            return $errors;
        }
        //insert ignore makes the statement issue warning instead of error
        $sql = "INSERT IGNORE INTO student(userid, password, name, school, edollar) VALUES (:userid, :password, :name, :school, :edollar)";
        
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $student->password = password_hash($student->password, PASSWORD_DEFAULT);

        $stmt->bindParam(':userid', $student->userid, PDO::PARAM_STR);
        $stmt->bindParam(':password', $student->password, PDO::PARAM_STR);
        $stmt->bindParam(':name', $student->name, PDO::PARAM_STR);
        $stmt->bindParam(':school', $student->school, PDO::PARAM_STR);
        $stmt->bindParam(':edollar', $student->edollar, PDO::PARAM_INT);

        $stmt->execute();
        //might want to add check if stmt succeeded
        
        $stmt = null;
        $conn = null; 
    }
}

?>