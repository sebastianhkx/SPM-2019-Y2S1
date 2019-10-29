<?php
require_once 'clearingLogic.php';

class RoundStatusDAO {

    public function retrieveAll() {
        // retrieves both rounds and returns an array of 2 round objects (r1 and r2)
        // round 1 is in index 0, round 2 is in index 1
        $sql = 'SELECT * from round_status';
        
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = null;

        while ($row = $stmt->fetch()) {
            $result[] = new RoundStatus((int)$row['round_num'], $row['status']);
        }

        $stmt = null;
        $conn = null; 
                 
        return $result;
    }

    public function retrieveCurrentActiveRound() {
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
            $result = new RoundStatus((int)$row['round_num'], $row['status']);
        }

        $stmt = null;
        $conn = null; 
                 
        return $result;
    }

    public function deleteAll() {
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

    public function setRound1(){
        //to be used to set round to 1 when bootstrapping
        $sql = 'UPDATE round_status SET status = "started" WHERE round_num = 1';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $stmt->execute();
        $count = $stmt->rowCount();

        $sql = 'UPDATE round_status SET status = "pending" WHERE round_num = 2';

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $stmt->execute();
        $count = $stmt->rowCount();

        $stmt = null;
        $conn = null; 

    }

    public function startRound() {
        // return True for successful update, otherwise False

        $rounds = $this->retrieveAll();

        $errors = [];
        if ($rounds[1]->status == 'ended') {
            $errors[] = 'round 2 ended';
        }

        if (!empty($errors)){
            //ends here if there are any errors
            return $errors;
        }
        
        if ($rounds[0]->status == 'started' or $rounds[1]->status == 'started') {
            // ends here if round 1 is already active
            return true;
        }

        if ($rounds[0]->status == 'pending'){
            $sql = 'UPDATE round_status SET status = "started" WHERE round_num = 1';
        }
        elseif ($rounds[1]->status == 'pending'){
            $sql = 'UPDATE round_status SET status = "started" WHERE round_num = 2';
        }
   
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':round_num', $round_num, PDO::PARAM_STR);
        
        $isUpdateOk = FALSE;
        if ($stmt->execute()) {
            $isUpdateOk = TRUE;
        }

        $stmt = null;
        $conn = null; 

        return $isUpdateOk;
    }

    public function stopRound() {
        // return True for successful update, otherwise False

        $rounds = $this->retrieveAll();

        $errors = [];
        if ($rounds[0]->status != 'started' && $rounds[1]->status != 'started') {
            $errors[] = 'round already ended';
        }

        if (!empty($errors)){
            //ends here if there are any errors
            return $errors;
        }
        
        if ($rounds[0]->status == 'started') {
            $round_num = 1;
            roundOneClearing();
        }
        elseif ($rounds[1]->status == 'started') {
            $round_num = 2;
            roundTwoClearing();
        }

        $sql = 'UPDATE round_status SET status = "ended" WHERE round_num = :round_num';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

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


