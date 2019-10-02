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
        //returns an array of bids by the user
        $sql = 'SELECT * FROM bid WHERE userid=:userid';
        
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = array();

        while ($row = $stmt->fetch()) {
            $result[] = new Bid($row['userid'], $row['amount'], $row['course'], $row['section']);
        }

        $stmt = null;
        $conn = null; 

        return $result;
    }

    public function retrieveByCourseSection($courseSection){
        //this takes in a array [course, section] and returns a array of bid objs
        //this function is used for round clearing
        
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

    public function add($bid){
        //takes in a bid object and returns array of errors if it fals bid validation

        $errors = [];

        //validation
        $studentDAO = new StudentDAO();
        if ($studentDAO->retrieve($bid->userid)==null){
            $errors[] = "invalid userid";
        }

        $edollar_array = explode('.',$bid->amount);//10.000 into ['10','000']
        if (isset($edollar_array[1])){
            $decimal_place = $edollar_array[1];
        }
        else{
            $decimal_place = 0;
        }
        $roundStatusDAO = new RoundStatusDAO();
        $current_round = $roundStatusDAO->retrieveCurrentActiveRound();//used for min amount and same school check
        if ($current_round->round_num == 1){
            $min_amount = 10;
        }
        else{
            //round 2 min amount
            //TODO currently set to 10
            $min_amount = 10;
        }
        if ($bid->amount<$min_amount || strlen($decimal_place)>2){
            //1st condition checks for bid > amount and 2nd condition checks for edollars decimal place
            $errors[] = "invalid amount";
        } 

        $courseDAO = new CourseDAO();
        $sectionDAO = new SectionDAO();

        $validCourse = TRUE;//used to determined if class and exam timetable checks should be done later
        $validSection = TRUE;//used to determined if class and exam timetable checks should be done later
        if (($courseDAO->retrieveByCourseId($bid->course))==null){
            $errors[] = "invalid course";
            $validCourse = FALSE;
            $validSection = FALSE;
        }
        else{
            if (($sectionDAO->retrieveBySection($bid)==null)){
                $errors[] = "invalid section";
                $validSection = FALSE;
            } 
        }

        if ($current_round->round_num == 1){
            //checks if school of bidded course is same as students school
            $studentObj = $studentDAO->retrieve($bid->userid);
            $courseDAO = new CourseDAO();
            $courseObj = $courseDAO->retrieveByCourseId($bid->course);
            if (!empty($courseObj) and $studentObj->school != $courseObj->school){
                $errors[] = 'not own school course';
            }
        }

        //retrieves list of current bids
        $bidObj_array = $this->retrieveByUser($bid->userid);
        if (!empty($bidObj_array)){
            //has existing bids, does not enter if there are no existing bids as it would be unnecessary to check
            //timetable clash
            if ($validSection){
                //checks if section is valid
                $bidSectionObj = $sectionDAO->retrieveBySection($bid);
                foreach ($bidObj_array as $bidObj){
                    $existingBidSectionObj = $sectionDAO->retrieveBySection($bid);
                    if ($bidObj->course != $bid->course and $bidSectionObj->day == $existingBidSectionObj->day and ($bidSectionObj->start == $existingBidSectionObj->start || $bidSectionObj->end == $existingBidSectionObj->end))
                        //1st condition checks that the course for the new bid and existing bid doesnt match, because new bid updates old bid and timetable clash wouldnt matter
                        //2nd condition checks if days clash, 3rd condition checks if start time clash, 4th checks if end time clash
                        $errors[] = 'class timetable clash';
                        break;
                }
                 //round 2 TODO check against course enrolled
            }
            
            
            //exam clash
            if ($validCourse){
                //checks if course is valid
                $bidCourseObj = $courseDAO->retrieveByCourseId($bid->course);
                foreach ($bidObj_array as $bidObj){
                    $existingBidCourseObj = $courseDAO->retrieveByCourseId($bidObj->course);
                    //1st condition checks that the course for the new bid and existing bid doesnt match, because new bid updates old bid and exam clash wouldnt matter
                    //2nd condition checks if exam date clash, 3rd condition checks if exam start time clash, 4th checks if exam end time clash
                    if ($bidObj->course != $bid->course and $bidCourseObj->exam_date == $existingBidCourseObj->exam_date and ($bidCourseObj->exam_start == $existingBidCourseObj->exam_start || $bidCourseObj->exam_end == $existingBidCourseObj->exam_end)){
                        $errors[] = 'exam timetable clash';
                        break;
                    }
                }
                //round 2 TODO check against course enrolled
            }
        }
      
        //incomplete prereq
        $prerequisiteDAO = new PrerequisiteDAO();
        $bidPrerequisiteCodeArray = $prerequisiteDAO->retrievePrerequisite($bid->course);
        if (!empty($bidPrerequisiteCodeArray)){
            foreach ($bidPrerequisiteCodeArray as $prerequisiteCode){
                if (completed_course($bid->userid, $prerequisiteCode)==FALSE){
                    $errors[] = 'incomplete prerequisite';
                    break;
                }
            }
        }

        //course completed
        $courseCompletedDAO = new CourseCompletedDAO();
        if ($courseCompletedDAO->completed_course($bid->userid, $bid->course)==TRUE){
            $errors[] = 'course completed';
        }

        //bidded more than 5
        $to_refund = 0;
        $existingBid = $this->retrieveByUseridCourse($bid->userid, $bid->course);//null if no existing bid
        if($existingBid == null ){
            //does not have existing bid, doesnt not need to check for updating bid as bids submmitted would still be 5
            if (sizeof($bidObj_array)>=5){
                $errors[] = 'section limit reached';
            }
        }

        //not enough edollars
        if ($studentDAO->retrieve($bid->userid)->edollar<$bid->amount){
            $errors[] = 'not enough e-dollars';
        }


        if (!empty($errors)){
            //ends here if there are any errors
            return $errors;
        }

        if($existingBid != null ){
            //has existing bid drops existing bid, drop function automatically refunds
            $this->drop($existingBid);
        }

        $studentDAO->deductEdollar($bid->userid, $bid->amount);
        $sql = 'INSERT IGNORE into bid(userid, amount, course, section) values (:userid, :amount, :course, :section)';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $bid->userid, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $bid->amount, PDO::PARAM_INT);
        $stmt->bindParam(':course', $bid->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $bid->section, PDO::PARAM_STR);

        $isAddOk = FALSE;
        if ($stmt->execute()) {
            $isAddOk = TRUE;
        }

        $stmt = null;
        $conn = null;
    }

    public function retrieveByUseridCourse($userid, $course){
        // returns bid 
        $sql = 'SELECT * FROM bid WHERE userid=:userid AND course=:course';
        
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = null;

        if($row = $stmt->fetch()){
            $result = new bid($row['userid'], $row['amount'], $row['course'], $row['section']);
        }

        $stmt = null;
        $conn = null; 

        return $result;
    }

    public function drop($bid){
        // this takes in a bidded obj that user/admin want to drop
        // returns array of errors if there is any, otherwise returns True

        // validation
        $errors = [];

        $course_dao = new CourseDAO();
        $course_exists = $course_dao->retrieveByCourseId($bid->course);
        $section_dao = new SectionDAO();
        $section_exists = $section_dao->retrieveBySection($bid);
        if ($course_exists == null) {
            $errors[] = "invalid course";
        }
        elseif ($section_exists == null) {
            $errors[] = "invalid section";
        }
        else {
            $matching_bid = False;
            $current_bids = $this->retrieveByUser($bid->userid);
            foreach ($current_bids as $current_bid) {
                if ($current_bid->course == $section_exists->course && $current_bid->section == $section_exists->section) {
                    $matching_bid = True;
                    break;
                }
            }
            if (!$matching_bid) {
                $errors[] = "no such bid";
            }
        }

        $round_status_dao = new RoundStatusDAO();
        $round_status = $round_status_dao->retrieveCurrentActiveRound();
        if ($round_status == null) {
            $errors[] = 'round ended';
        }

        if (!empty($errors)){
            return $errors;
        }

        // refund the student first before deleting bid
        $student_dao = new StudentDAO();
        $to_refund = $this->checkExistingBid($bid);
        $student_dao->addEdollar($bid->userid, $to_refund);

        // delete the bid
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
        // this takes in a userid , course and section
        // **no, this takes in a bid object without caring about the amount**
        // returns amount bidded on existing bid, 0 if no existing bids
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