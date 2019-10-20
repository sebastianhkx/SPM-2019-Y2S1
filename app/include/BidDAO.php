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
        
        $sql = 'SELECT * FROM bid WHERE course=:course and section=:section order by amount DESC';

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
        //takes in a bid object and returns array of errors if it fails bid validation

        $errors = [];

        //input validation
        //username validation bootstrap + JSON
        $studentDAO = new StudentDAO();
        if ($studentDAO->retrieve($bid->userid)==null){
            $errors[] = "invalid userid";
        }
        //amount validation bootstrap + JSON
        $edollar_array = explode('.',$bid->amount);//10.000 into ['10','000']
        if (isset($edollar_array[1])){
            $decimal_place = $edollar_array[1];
        }
        else{
            $decimal_place = 0;
        }
        if ($bid->amount<10 || strlen($decimal_place)>2){
            //1st condition checks for bid > amount and 2nd condition checks for edollars decimal place
            $errors[] = "invalid amount";
        } 

        $courseDAO = new CourseDAO();
        $sectionDAO = new SectionDAO();
        //course validation bootstrap + JSON
        if (($courseDAO->retrieveByCourseId($bid->course))==null){
            $errors[] = "invalid course";
            $validCourse = FALSE;
            $validSection = FALSE;
        }
        else{
            //section validation bootstrap + JSON
            if (($sectionDAO->retrieveBySection($bid)==null)){
                $errors[] = "invalid section";
                $validSection = FALSE;
            } 
        }
        if (!empty($errors)){
            return $errors;
        }

        //logic validation starts here, does not enter if there are any errors with input as return is called
        $roundStatusDAO = new RoundStatusDAO();
        $current_round = $roundStatusDAO->retrieveCurrentActiveRound();

        if ($current_round != null && $current_round->round_num == 1){
            //same school validation bootstrap + JSON
            $studentObj = $studentDAO->retrieve($bid->userid);
            $courseDAO = new CourseDAO();
            $courseObj = $courseDAO->retrieveByCourseId($bid->course);
            if ($courseObj!=null and $studentObj!=null){
                if (!empty($courseObj) && $studentObj->school != $courseObj->school){
                    $errors[] = 'not own school course';
                }
            }
        }

        //retrieves list of current bids
        $bidObj_array = $this->retrieveByUser($bid->userid);
        if (!empty($bidObj_array)){
            //has existing bids, does not enter if there are no existing bids as it would be unnecessary to check
            //timetable clash BOOTSTRAP + JSON
            $bidSectionObj = $sectionDAO->retrieveBySection($bid);//new bid
            $newStart = $bidSectionObj->start;
            $newEnd = $bidSectionObj->end;
            foreach ($bidObj_array as $bidObj){
                $existingBidSectionObj = $sectionDAO->retrieveBySection($bidObj);//existing bid
                $existingStart = $existingBidSectionObj->start;
                $existingEnd = $existingBidSectionObj->end;
                if ($bidObj->course != $bid->course && $bidSectionObj->day == $existingBidSectionObj->day && (($newStart<$existingEnd and $newStart>$existingStart) || ($existingStart<$newEnd and $existingStart>$newStart) || ($newStart == $existingStart || $newEnd == $existingEnd)))
                    //1st condition checks that the course for the new bid and existing bid doesnt match, because new bid updates old bid and timetable clash wouldnt matter
                    //2nd condition checks if days clash, 3rd condition checks if new start between existing start end, 4th checks if existing start between new start end, 5th checks if either start end overlaps
                    $errors[] = 'class timetable clash';
                    break;
            }
            
            
            //exam clash BOOTSTRAP + JSON
            $bidCourseObj = $courseDAO->retrieveByCourseId($bid->course);
            $newStart = $bidCourseObj->exam_start;
            $newEnd = $bidCourseObj->exam_end;
            foreach ($bidObj_array as $bidObj){
                $existingBidCourseObj = $courseDAO->retrieveByCourseId($bidObj->course);
                $existingStart = $existingBidCourseObj->exam_start;
                $existingEnd = $existingBidCourseObj->exam_end;
                //1st condition checks that the course for the new bid and existing bid doesnt match, because new bid updates old bid and exam clash wouldnt matter
                //2nd condition checks if exam date clash, 3rd condition checks if new start between existing start end, 4th checks if existing start between new start end, 5th checks if either start end overlaps
                if ($bidObj->course != $bid->course && $bidCourseObj->exam_date == $existingBidCourseObj->exam_date && (($newStart<$existingEnd and $newStart>$existingStart) || ($existingStart<$newEnd and $existingStart>$newStart) || ($newStart == $existingStart || $newEnd == $existingEnd))){
                    $errors[] = 'exam timetable clash';
                    break;
                }
            }
                
        }

        //incomplete prereq BOOTSTRAP + JSON
        $prerequisiteDAO = new PrerequisiteDAO();
        $courseCompletedDAO = new CourseCompletedDAO();
        $bidPrerequisiteCodeArray = $prerequisiteDAO->retrievePrerequisite($bid->course);
        if (!empty($bidPrerequisiteCodeArray)){
            foreach ($bidPrerequisiteCodeArray as $prerequisiteCode){
                if ($courseCompletedDAO->completed_course($bid->userid, $prerequisiteCode)==FALSE){
                    $errors[] = 'incomplete prerequisites';
                    break;
                }
            }
        }

        //course completed BOOTSTRAP + JSON
        if ($courseCompletedDAO->completed_course($bid->userid, $bid->course)==TRUE){
            $errors[] = 'course completed';
        }

        //bidded more than 5 BOOTSTRAP + JSON
        $to_refund = 0;
        $existingBid = $this->retrieveByUseridCourse($bid->userid, $bid->course);//null if no existing bid
        if($existingBid == null ){
            //does not have existing bid, doesnt not need to check for updating bid as bids submmitted would still be 5
            if (sizeof($bidObj_array)>=5){
                $errors[] = 'section limit reached';
            }
        }

        $edollars = $studentDAO->retrieve($bid->userid)->edollar;
        if ($existingBid != null){
            $edollars += $existingBid->amount;
        }
        //not enough edollars BOOTSTRAP+JSON
        if ($studentDAO->retrieve($bid->userid)!=null){
            if ($edollars<$bid->amount){
                $errors[] = 'insufficient e$';
            }
        }

        //bid too low for round 2 JSON
        if ($current_round != null && $current_round->round_num==2){
            $r2BidDAO = new R2BidDAO();
            $min = $r2BidDAO->getminimunprice($bid);
            // var_dump($min);
            if ($bid->amount < $min){
                $errors[] = 'bid too low';
            }
        }

        //round 2 TODO check against course enrolled JSON
        $courseEnrolledDAO = new CourseEnrolledDAO();
        if ($courseEnrolledDAO->retrieveByUseridCourse($bid->userid, $bid->course)!=null){
            $errors[] = 'course enrolled';

            //check for timetable and exam clash against enrolled
            $courseEnrolledObj_array = $courseEnrolledDAO->retrieveByUserid($bid->userid);
            if (!empty($courseEnrolledObj_array)){
                //has existing bids, does not enter if there are no existing bids as it would be unnecessary to check
                //timetable clash BOOTSTRAP + JSON
                $bidSectionObj = $sectionDAO->retrieveBySection($bid);//new bid
                $newStart = $bidSectionObj->start;
                $newEnd = $bidSectionObj->end;
                foreach ($courseEnrolledObj_array as $courseEnrolledObj){
                    $existingBidSectionObj = $sectionDAO->retrieveBySection($courseEnrolledObj);//existing bid
                    $existingStart = $existingBidSectionObj->start;
                    $existingEnd = $existingBidSectionObj->end;
                    if ($courseEnrolledObj->course != $bid->course && $bidSectionObj->day == $existingBidSectionObj->day && (($newStart<$existingEnd and $newStart>$existingStart) || ($existingStart<$newEnd and $existingStart>$newStart) || ($newStart == $existingStart || $newEnd == $existingEnd)))
                        //1st condition checks that the course for the new bid and existing bid doesnt match, because new bid updates old bid and timetable clash wouldnt matter
                        //2nd condition checks if days clash, 3rd condition checks if new start between existing start end, 4th checks if existing start between new start end, 5th checks if either start end overlaps
                        $errors[] = 'class timetable clash';
                        break;
                }
                
                
                //exam clash BOOTSTRAP + JSON
                $bidCourseObj = $courseDAO->retrieveByCourseId($bid->course);
                $newStart = $bidCourseObj->exam_start;
                $newEnd = $bidCourseObj->exam_end;
                foreach ($courseEnrolledObj_array as $courseEnrolledObj){
                    $existingBidCourseObj = $courseDAO->retrieveByCourseId($courseEnrolledObj->course);
                    $existingStart = $existingBidCourseObj->exam_start;
                    $existingEnd = $existingBidCourseObj->exam_end;
                    //1st condition checks that the course for the new bid and existing bid doesnt match, because new bid updates old bid and exam clash wouldnt matter
                    //2nd condition checks if exam date clash, 3rd condition checks if new start between existing start end, 4th checks if existing start between new start end, 5th checks if either start end overlaps
                    if ($courseEnrolledObj->course != $bid->course && $bidCourseObj->exam_date == $existingBidCourseObj->exam_date && (($newStart<$existingEnd and $newStart>$existingStart) || ($existingStart<$newEnd and $existingStart>$newStart) || ($newStart == $existingStart || $newEnd == $existingEnd))){
                        $errors[] = 'exam timetable clash';
                        break;
                    }
                }
                    
            }
        }

        //round ended (no active round) JSON
        if ($current_round==null){
            $errors[] = 'round ended';
        }

        //no vacancy check for round 2 JSON
        $courseEnrolledObjs = $courseEnrolledDAO->retrieveByCourseSection([$bid->course, $bid->section]);
        //var_dump($courseEnrolledObjs);
        if ($courseEnrolledObjs != null){
            $enrolled = sizeof($courseEnrolledObjs);
        }
        else{
            $enrolled = 0;
        }
        $size = $sectionDAO->retrieveBySection($bid)->size;
        if ($size-$enrolled<=0){
            $errors[] = 'no vacancy';
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
        //update min bid in round 2
        //if current round is 2
        if($current_round->round_num == 2){
            $r2BidDAO = new R2BidDAO();
            $r2Info = $r2BidDAO->getr2bidinfo($bid);
            $vacancy = $r2Info->vacancy;
            $oldMin = $r2Info->min_amount;
            $newMin = $this->getRoundTwoSuccessfullPrice($bid, $vacancy-1)+1;
            // var_dump($newMin,'new', $oldMin);
            // var_dump('test', 10>'15');
            if ($newMin > $oldMin){
                $bidInfoObj = new R2Bid($bid->course, $bid->section, $newMin, $vacancy);
                // var_dump($bidInfoObj);
                $r2BidDAO->updateBidinfo($bidInfoObj);
            }
        }
        return $isAddOk;
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

        // input validation starts here
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

        $studentDAO = new StudentDAO();
        if ($studentDAO->retrieve($bid->userid)==null){
            $errors[] = "invalid userid";
        }

        if (!empty($errors)){
            return $errors;
        }

        // logical validation starts here, does not enter if there are input validation errors

        $round_status_dao = new RoundStatusDAO();
        $round_status = $round_status_dao->retrieveCurrentActiveRound();
        if ($round_status == null) {
            $errors[] = 'round ended';
        }

        if ($course_exists != null && $section_exists != null && $round_status != null) {
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

    public function getRoundTwoSuccessfullPrice($bidObj, $vacancy){
        #this function takes in a bid obj and vacancy integer, any bid amount > successfull price are bids that will succeed
        $sql = 'SELECT amount from bid where course=:course and section=:section order by amount DESC limit 1 offset :vacancy';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $bidObj->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $bidObj->section, PDO::PARAM_STR);
        $stmt->bindParam(':vacancy', $vacancy, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = 0;

        if ($row = $stmt->fetch()){
            $result = $row['amount'];
        }
        
        return $result;

    }

    
    public function bootstrapadd($bid){
        //takes in a bid object and returns array of errors if it fals bid validation

        $errors = [];

        //input validation
        //username validation bootstrap + JSON
        $studentDAO = new StudentDAO();
        if ($studentDAO->retrieve($bid->userid)==null){
            $errors[] = "invalid userid";
        }
        //amount validation bootstrap + JSON
        $edollar_array = explode('.',$bid->amount);//10.000 into ['10','000']
        if (isset($edollar_array[1])){
            $decimal_place = $edollar_array[1];
        }
        else{
            $decimal_place = 0;
        }
        if ($bid->amount<10 || strlen($decimal_place)>2){
            //1st condition checks for bid > amount and 2nd condition checks for edollars decimal place
            $errors[] = "invalid amount";
        } 

        $courseDAO = new CourseDAO();
        $sectionDAO = new SectionDAO();
        //course validation bootstrap + JSON
        if (($courseDAO->retrieveByCourseId($bid->course))==null){
            $errors[] = "invalid course";
            $validCourse = FALSE;
            $validSection = FALSE;
        }
        else{
            //section validation bootstrap + JSON
            if (($sectionDAO->retrieveBySection($bid)==null)){
                $errors[] = "invalid section";
                $validSection = FALSE;
            } 
        }
        if (!empty($errors)){
            return $errors;
        }

        //logic validation starts here, does not enter if there are any errors with input as return is called
        $roundStatusDAO = new RoundStatusDAO();
        $current_round = $roundStatusDAO->retrieveCurrentActiveRound();

        if ($current_round->round_num == 1){
            //same school validation bootstrap + JSON
            $studentObj = $studentDAO->retrieve($bid->userid);
            $courseDAO = new CourseDAO();
            $courseObj = $courseDAO->retrieveByCourseId($bid->course);
            if ($courseObj!=null and $studentObj!=null){
                if (!empty($courseObj) && $studentObj->school != $courseObj->school){
                    $errors[] = 'not own school course';
                }
            }
        }

        //retrieves list of current bids
        $bidObj_array = $this->retrieveByUser($bid->userid);
        if (!empty($bidObj_array)){
            //has existing bids, does not enter if there are no existing bids as it would be unnecessary to check
            //timetable clash BOOTSTRAP + JSON
            $bidSectionObj = $sectionDAO->retrieveBySection($bid);//new bid
            $newStart = $bidSectionObj->start;
            $newEnd = $bidSectionObj->end;
            foreach ($bidObj_array as $bidObj){
                $existingBidSectionObj = $sectionDAO->retrieveBySection($bidObj);//existing bid
                $existingStart = $existingBidSectionObj->start;
                $existingEnd = $existingBidSectionObj->end;
                if ($bidObj->course != $bid->course && $bidSectionObj->day == $existingBidSectionObj->day && (($newStart<$existingEnd and $newStart>$existingStart) || ($existingStart<$newEnd and $existingStart>$newStart) || ($newStart == $existingStart || $newEnd == $existingEnd)))
                    //1st condition checks that the course for the new bid and existing bid doesnt match, because new bid updates old bid and timetable clash wouldnt matter
                    //2nd condition checks if days clash, 3rd condition checks if new start between existing start end, 4th checks if existing start between new start end, 5th checks if either start end overlaps
                    $errors[] = 'class timetable clash';
                    break;
            }
            
            
            //exam clash BOOTSTRAP + JSON
            $bidCourseObj = $courseDAO->retrieveByCourseId($bid->course);
            $newStart = $bidCourseObj->exam_start;
            $newEnd = $bidCourseObj->exam_end;
            foreach ($bidObj_array as $bidObj){
                $existingBidCourseObj = $courseDAO->retrieveByCourseId($bidObj->course);
                $existingStart = $existingBidCourseObj->exam_start;
                $existingEnd = $existingBidCourseObj->exam_end;
                //1st condition checks that the course for the new bid and existing bid doesnt match, because new bid updates old bid and exam clash wouldnt matter
                //2nd condition checks if exam date clash, 3rd condition checks if new start between existing start end, 4th checks if existing start between new start end, 5th checks if either start end overlaps
                if ($bidObj->course != $bid->course && $bidCourseObj->exam_date == $existingBidCourseObj->exam_date && (($newStart<$existingEnd and $newStart>$existingStart) || ($existingStart<$newEnd and $existingStart>$newStart) || ($newStart == $existingStart || $newEnd == $existingEnd))){
                    $errors[] = 'exam timetable clash';
                    break;
                }
            }
                
        }

        //incomplete prereq BOOTSTRAP + JSON
        $prerequisiteDAO = new PrerequisiteDAO();
        $courseCompletedDAO = new CourseCompletedDAO();
        $bidPrerequisiteCodeArray = $prerequisiteDAO->retrievePrerequisite($bid->course);
        if (!empty($bidPrerequisiteCodeArray)){
            foreach ($bidPrerequisiteCodeArray as $prerequisiteCode){
                if ($courseCompletedDAO->completed_course($bid->userid, $prerequisiteCode)==FALSE){
                    $errors[] = 'incomplete prerequisites';
                    break;
                }
            }
        }

        //course completed BOOTSTRAP + JSON
        if ($courseCompletedDAO->completed_course($bid->userid, $bid->course)==TRUE){
            $errors[] = 'course completed';
        }

        //bidded more than 5 BOOTSTRAP + JSON
        $to_refund = 0;
        $existingBid = $this->retrieveByUseridCourse($bid->userid, $bid->course);//null if no existing bid
        if($existingBid == null ){
            //does not have existing bid, doesnt not need to check for updating bid as bids submmitted would still be 5
            if (sizeof($bidObj_array)>=5){
                $errors[] = 'section limit reached';
            }
        }

        $edollars = $studentDAO->retrieve($bid->userid)->edollar;
        if ($existingBid != null){
            $edollars += $existingBid->amount;
        }
        //not enough edollars BOOTSTRAP+JSON
        if ($studentDAO->retrieve($bid->userid)!=null){
            if ($edollars<$bid->amount){
                $errors[] = 'not enough e-dollars';
            }
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

    public function bidStatus($bid){
        $r2BidDAO = new R2BidDAO();
        $vacancy = $r2BidDAO->getr2bidinfo($bid)->vacancy;
        $price = $this->getRoundTwoSuccessfullPrice($bid, $vacancy);
        if ($bid->amount > $price){
            return 'Successfull';
        }
        else{
            return 'Unsuccessfull. Bid too low';
        }
    }
}