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
            $result[] = new Result($row['userid'], $row['amount'], $row['course'], $row['section'], $row['result'],$row['round_num']);
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
            $result[] = new Result($row['userid'], $row['amount'], $row['course'], $row['section'], $row['result'],$row['round_num']);
        }

        $stmt = null;
        $conn = null; 

        return $result;
    }

    public function retrieveByCourseEnrolled($courseEnrolled){
        //takes in a CourseEnrolled Object
        $sql = 'SELECT * FROM bid_result WHERE userid = :userid and course = :course and section = :section';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $courseEnrolled->userid, PDO::PARAM_STR);
        $stmt->bindParam(':course', $courseEnrolled->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $courseEnrolled->section, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = null;

        while($row = $stmt->fetch()) {
            $result = new Result($row['userid'], $row['amount'], $row['course'], $row['section'], $row['result'],$row['round_num']);
        }

        $stmt = null;
        $conn = null; 

        return $result;
    }

    public function retrieveByCourseSection($courseSection) {
        //takes in a CourseSection array
        $sql = 'SELECT * FROM bid_result WHERE course = :course and section = :section';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $courseSection[0], PDO::PARAM_STR);
        $stmt->bindParam(':section', $courseSection[1], PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = null;

        while($row = $stmt->fetch()) {
            $result[] = new Result($row['userid'], $row['amount'], $row['course'], $row['section'], $row['result'],$row['round_num']);
        }

        $stmt = null;
        $conn = null; 

        return $result;
    }

    public function add($result){
        //takes in a result object and updates tha table
        $sql = 'INSERT IGNORE INTO bid_result(userid, amount, course, section, result, round_num) values (:userid, :amount, :course, :section, :result, :round_num)';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $stmt->bindParam(':userid', $result->userid, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $result->amount, PDO::PARAM_STR);
        $stmt->bindParam(':course', $result->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $result->section, PDO::PARAM_STR);
        $stmt->bindParam(':result', $result->result, PDO::PARAM_STR);
        $stmt->bindParam(':round_num', $result->round_num, PDO::PARAM_INT);

        $isAddOk = FALSE;
        if ($stmt->execute()) {
            $isAddOk = TRUE;
        }

        $stmt = null;
        $conn = null;

        return $isAddOk;
    }

    public function delete($result){
        //takes in Result obj
        $sql = 'DELETE from bid_result where userid = :userid and course = :course and section=:section and result = :result and round_num = :round_num';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $result->userid, PDO::PARAM_STR);
        $stmt->bindParam(':course', $result->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $result->section, PDO::PARAM_STR);
        $stmt->bindParam(':result', $result->result, PDO::PARAM_STR);
        $stmt->bindParam(':round_num', $result->round_num, PDO::PARAM_STR);
        
        $isDeleteOk = FALSE;
        if ($stmt->execute()) {
            $isDeleteOk = TRUE;
        }

        $stmt = null;
        $conn = null; 

        return $isDeleteOk;
    }
    
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

    public function retrieveBySuccessfullyCourseEnrolled($courseEnrolled){
        //takes in a CourseEnrolled Object
        $sql = 'SELECT * FROM bid_result WHERE userid = :userid and course = :course and section = :section and result="success"';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $courseEnrolled->userid, PDO::PARAM_STR);
        $stmt->bindParam(':course', $courseEnrolled->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $courseEnrolled->section, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = null;

        while($row = $stmt->fetch()) {
            $result = new Result($row['userid'], $row['amount'], $row['course'], $row['section'], $row['result'],$row['round_num']);
        }

        $stmt = null;
        $conn = null; 

        return $result;
    }
}


