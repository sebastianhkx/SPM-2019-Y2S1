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
            $result[] = new student($row['userid'], $row['password'], $row['name'], $row['school'],['edollar']);
        }

        $stmt = null;
        $conn = null; 
                 
        return $result;
    }

    public function removeAll(){
        $sql = 'TRUNCATE TABLE student';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $count = $stmt->rowCount();
    }

    public function add($student){
        //takes in student object and adds it to the database
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
        $stmt->bindParam(':edollars', $student->edollars, PDO::PARAM_INT);

        $stmt->execute();
        //might want to add check if stmt succeeded
    }
}

?>