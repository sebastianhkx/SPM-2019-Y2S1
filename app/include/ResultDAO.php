<?php

class ResultDAO {

    public function retrieveAll() {
        $sql = 'SELECT * FROM bid_result ORDER BY `userid`';
        
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch()) {
            $result[] = new Result($row['userid'], $row['amount'], $row['course'], $row['section'], $row['result'],$row['round']);
        }

        $stmt = null;
        $conn = null; 
                 
        return $result;
    }

    public function retrieveByUser($userid) {
        //this takes in a userid string
        $sql = 'SELECT * FROM bid_result WHERE userid=:userid';
        
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while($row = $stmt->fetch()) {
            $result[] = new Result($row['userid'], $row['amount'], $row['course'], $row['section'], $row['result'],$row['round']);
        }

        $stmt = null;
        $conn = null; 

        return $result;
    }

    ## to add function for adding clearinglogic successful bids

    public function deleteAll(){
        $sql = 'TRUNCATE TABLE bid_result';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $count = $stmt->rowCount();

        $stmt = null;
        $conn = null; 
    }
}


