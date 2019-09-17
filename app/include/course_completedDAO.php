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
        
        public function completed_prerequisite($userid, $courseid){
            /*
            does a recursive check if user has completed prerequiste for a course
            ex1: course C has prereq B which has prereq A, function($userid, C) will check for B which calls recurisve function($userid, B)
            will fail if user completed B but not its prereq A when function($userid, C)
            */
            $prerequisiteDAO = new PrerequisiteDAO;
            $prerequisites = $prerequisiteDAO->retrievePrerequiste($courseid);
            if (empty($prerequisites)){
                return True;
            }
            else{
                //has prerequistes
                foreach ($prerequisite as $prereqid){//does recursive check
                    $recursive_check = $this->completed_prerequisite($userid, $prereqid);
                    if ($recursive_check == FALSE){
                        //returns false if any of the nested prereq isn't completed
                        return FALSE;
                    }
                }
                return TRUE;
            }
        }
    }
?>