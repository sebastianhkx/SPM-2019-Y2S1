<?php

class BidDAO {

    public  function retrieveAll() {
        $sql = 'SELECT * FROM bid ORDER BY `amount` DESC';
        
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch()) {
            $result[] = new Bid($row['userid'], $row['amount'], $row['course'], $row['section']);
        }

        $stmt = null;
        $conn = null; 
                 
        return $result;
    }

    public function deleteAll(){
        $sql = 'TRUNCATE TABLE bid';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $count = $stmt->rowCount();

        $stmt = null;
        $conn = null; 
    }

    public function add($bid){
        //takes in argument bid obj
        $sql = 'INSERT IGNORE into bid(userid, amount, course, section) values (:userid, :amount, :course, :section)';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $bid->userid, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $bid->amount, PDO::PARAM_INT);
        $stmt->bindParam(':course', $bid->code, PDO::PARAM_STR);
        $stmt->bindParam(':section', $bid->section, PDO::PARAM_STR);

        $stmt->execute();

        $stmt = null;
        $conn = null; 

    }
}