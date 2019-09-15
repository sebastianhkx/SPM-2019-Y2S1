<?php

class CourseCompletedDAO(){

    public function deleteAll(){
        $sql = 'TRUNCATE TABLE course_completed';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $count = $stmt->rowCount();

    }

    public function add($courseCompleted){
        //takes in CourseCompleted obj
        $sql = 'INSERT IGNORE into course_completed(userid, code) values (:userid, :code)';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $courseCompleted->userid, PDO::PARAM_STR);
        $stmt->bindParam(':code', $courseCompleted->code, PDO::PARAM_STR);

        $stmt->execute();
    }


}

?>