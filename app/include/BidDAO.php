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

    public function retrieveByCourseSection($courseSection){
        //this takes in a array [course, section] and returns a array of bid objs
        
        $sql = 'SELECT * FROM bid WHERE course=:course and section=:section';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $courseSection[0], PDO::PARAM_STR);
        $stmt->bindParam(':section', $courseSection[1], PDO::PARAM_STR);

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
        $errors = [];
        $student_dao = new StudentDAO();
        $to_refund = 0;
        $amount_old = $this->checkExistingBid($bid_input);
        if($amount_old != 0){
            $to_refund = $amount_old - ($bid_input->amount);
            $student_dao->addEdollar($bid_input->userid, $to_refund);
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

    public function drop($bid){
        // this takes in a bidded obj that user/admin want to drop
        $sql = 'DELETE from bid where userid=:userid AND course=:course AND section=:section';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $bid->userid, PDO::PARAM_STR);
        $stmt->bindParam(':course', $bid->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $bid->section, PDO::PARAM_STR);
        
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

    public function retrieveBiddedSections(){
        //this returns a list of distinct (course, sections) in the bid table as an array of array [course, section]
        //used for check how many course and sections need to be resolved in round clearing
        $sql = 'select distinct course, section from bid';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = [];

        while ($row = $stmt->fetch()){
            $result[] = [$row['course'], $row['section']];
        }
        
        return $result;
    }

    public function getClearingPrice($bidObj, $vacancy){

        $sql = 'SELECT amount FROM bid WHERE course=:course and section=:section order by amount DESC limit 1 offset :vacancy';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $bidObj->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $bidObj->section, PDO::PARAM_STR);
        $stmt->bindParam(':vacancy', $vacancy, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = null;

        if ($row = $stmt->fetch()){
            $result = $row['amount'];
        }
        
        return $result;

    }
}