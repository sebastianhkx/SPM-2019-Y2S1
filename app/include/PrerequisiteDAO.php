<?php

class PrequisiteDAO{

    public function deleteAll(){
        $sql = 'TRUNCATE TABLE prequisite';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $count = $stmt->rowCount();

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
    }

}

?>