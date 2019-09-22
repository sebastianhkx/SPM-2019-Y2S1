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

    public function retrieveByUser($userid) {
        //this takes in a userid string
        $sql = 'SELECT * FROM bid WHERE userid=:userid';
        
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);

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

    public function add($bid_input){
        //takes in argument bid obj
        //$bid_input is new bid;
        $student_dao = new StudentDAO();
        $to_refund = 0;
        $amount_old = $this->checkExistingBid($bid_input);
        if($amount_old != 0){
            $to_refund = $amount_old - ($bid_input->amount);
            $student_dao->addEdollar($bid_input->userid, $to_refund);
            //"UPDATE MyGuests SET lastname='Doe' WHERE id=2";
            $sql = 'UPDATE bid SET amount=:amount WHERE userid=:userid AND course=:course AND section=:section' ;
        }
        else{
            $student_dao->deductEdollar($bid_input->userid, $bid_input->amount);
            $sql = $sql = 'INSERT IGNORE into bid(userid, amount, course, section) values (:userid, :amount, :course, :section)';
        }
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $bid_input->userid, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $bid_input->amount, PDO::PARAM_INT);
        $stmt->bindParam(':course', $bid_input->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $bid_input->section, PDO::PARAM_STR);

        $isAddOk = FALSE;
        if ($stmt->execute()) {
            $isAddOk = TRUE;
        }

        $stmt = null;
        $conn = null;

        return $isAddOk;
    }

    public function drop($userid){
        $sql = 'DELETE from bid where userid=:userid';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $student->userid, PDO::PARAM_STR);
        
        $isDeleteOk = FALSE;
        if ($stmt->execute()) {
            $isDeleteOk = TRUE;
        }

        $stmt = null;
        $conn = null; 

        return $isDeleteOk;
    }

    
    public function checkExistingBid($bid_input) {
        //this takes in a userid , course and section and returns amount bidded on existing bid, 0 if no existing bids
        $sql = 'SELECT amount FROM bid WHERE userid=:userid AND course=:course AND section=:section';
        
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $bid_input ->userid, PDO::PARAM_STR);
        $stmt->bindParam(':course', $bid_input ->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $bid_input ->section, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $amount = 0;

        if($row = $stmt->fetch()){
            $amount = $row['amount'];
        }

        $stmt = null;
        $conn = null; 

        return $amount;
    }

}