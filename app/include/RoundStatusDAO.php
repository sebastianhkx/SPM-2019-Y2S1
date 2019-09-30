<?php

class RoundStatusDAO {

    public function retrieveCurrentActiveRound(){
        // checks if there is an active bidding round currently
        // return round_status object if there is, null otherwise
        $sql = 'SELECT * from round_status where status = "started"';
        
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = null;

        if ($row = $stmt->fetch()) {
            $result = new RoundStatus($row['round_num'], $row['status']);
        }

        $stmt = null;
        $conn = null; 
                 
        return $result;
    }

    public function deleteAll(){
        $sql = 'TRUNCATE TABLE round_status';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $count = $stmt->rowCount();

        $stmt = null;
        $conn = null; 
    }

    public function updateRoundStatus($round_num, $status) {
        // takes in an int for round_num, and the desired status
        // return True for successful update, otherwise False
        $sql = 'UPDATE round_status SET status = :status WHERE round_num = :round_num';
    
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':round_num', $round_num, PDO::PARAM_STR);
        
        $isUpdateOk = FALSE;
        if ($stmt->execute()) {
            $isUpdateOk = TRUE;
        }

        $stmt = null;
        $conn = null; 

        return $isUpdateOk;
    }
}


