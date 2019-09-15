<?php

    class Course_completedDAO {

        public function retrieveAll(){
            $sql = 'SELECT * FROM course_completed ORDER BY `userid`';

            $connMgr = new ConnectionManager();     
            $conn = $connMgr->getConnection();

            $stmt = $conn->prepare($sql);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();

            $arr = array();

            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $arr[] = [$row['userid'],$row['code']];
            }

            $conn = null;
            $stmt = null;
            return $arr;

        }

        public function retrieve($userid){
            $connMgr = new ConnectionManager();  
            $conn = $connMgr->getConnection();

            $sql = "select * from course_completed where userid = :userid";
            $stmt = $conn->prepare($sql);

            $stmt->bindparam(':userid',$userid,PDO::PARAM_STR);

            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $stmt->execute();
            
            $result = [];

            while ($row=$stmt->fetch()){
                $result[] = $row['code'];
            }

            $conn = null;
            $stmt = null;
            return $result;
        }
    }





?>