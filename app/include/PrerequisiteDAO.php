<?php

class PrerequisiteDAO{

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
        //takes in prerequsite obj
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

}

?>