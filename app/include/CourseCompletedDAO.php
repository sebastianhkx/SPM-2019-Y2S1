<?php

class CourseCompletedDAO {

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
        //this funtion takes in a single string 'userid' and return an array of course that user has completed
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

    public function delete($userid, $courseid){
        //this function takes in two single string 'userid' and course code
        //no return 
        $connMgr = new ConnectionManager();  
        $conn = $connMgr->getConnection();

        $sql = "DELETE from course_completed where userid = :userid and code = :courseid";
        $stmt = $conn->prepare($sql);

        $stmt->bindparam(':userid',$userid,PDO::PARAM_STR);
        $stmt->bindparam(':courseid',$courseid,PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $status = $stmt->execute();
        //var_dump($status);


        $conn = null;
        $stmt = null;
    }
    
    public function deleteAll(){
        $sql = 'TRUNCATE TABLE course_completed';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $count = $stmt->rowCount();

    }

    public function completed_course($userid, $courseid){
        $connMgr = new ConnectionManager();  
        $conn = $connMgr->getConnection();

        $sql = "select * from course_completed where userid = :userid and code = :courseid";
        $stmt = $conn->prepare($sql);

        $stmt->bindparam(':userid',$userid,PDO::PARAM_STR);
        $stmt->bindparam(':courseid',$courseid,PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        
        $result = False;

        if ($row=$stmt->fetch()){
            $result = True;
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
        $prerequisites = $prerequisiteDAO->retrievePrerequisite($courseid);
        if (empty($prerequisites)){//has prerequisites
            return True; //base case if course has not prerequisites
        }
        else{
            //has prerequistes
            foreach ($prerequisites as $prereqid){
                $recursive_check = $this->recursive_check($userid, $prereqid);//does recursive check for all nested prereq of prereq course is compeleted
                if ($recursive_check == FALSE){
                    //returns false if any of the nested prereq isn't completed
                    return FALSE;
                }
            }
            return TRUE;
        }
    }

    public function recursive_check($userid, $courseid){
        /*
        recursive check used in completed_prerequisite method
        */
        if ($this->completed_course($userid, $courseid)==null){
            return FALSE;
        }
        $prerequisiteDAO = new PrerequisiteDAO;
        $prerequisites = $prerequisites = $prerequisiteDAO->retrievePrerequisite($courseid);
        if (!empty($prerequisites)){
            foreach ($prerequisites as $prerequisite){
                if ($this->completed_course($userid, $courseid)==null){
                    return FALSE;
                }
                $check = recursive_check($userid, $prerequisite);
                if ($check == FALSE){
                    return FALSE;
                }
            }
        }
        return TRUE;
    }

    public function add($courseCompleted){
        //takes in CourseCompleted obj
        //returns null if data validation passes returns array of errors if fails
        $errors = [];

        $studentDAO = new StudentDAO();
        if ($studentDAO->retrieve($courseCompleted->userid)==null){
            $errors[] = "invalid userid";
        }
        $courseDAO = new CourseDAO();
        if (($courseDAO->retrieveByCourseId($courseCompleted->code))==null){
            $errors[] = "invalid course";
        }
        
        if(!empty($errors)){
            return $errors;
        }

        $sql = 'INSERT IGNORE into course_completed(userid, code) values (:userid, :code)';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $courseCompleted->userid, PDO::PARAM_STR);
        $stmt->bindParam(':code', $courseCompleted->code, PDO::PARAM_STR);

        $stmt->execute();

        $stmt = null;
        $conn = null; 
    }


}

?>