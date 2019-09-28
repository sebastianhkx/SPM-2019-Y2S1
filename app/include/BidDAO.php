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

        if ($row = $stmt->fetch()) {
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
        //takes in new bid object

        $errors = [];

        //validation
        if ($bid_input->amount < 10.00){
            $errors[] = "invalid amount";
        }

        $student_dao = new StudentDAO();
        $student = $student_dao->retrieve($bid_input->userid);
        if ($bid_input->amount > $student->edollar){
            $errors[] = "not enough e-dollar";
        }

        // performs 2 validation checks
        // 1. check valid course first
        // 2. then check valid section
        $course_and_section_valid = True;
        $course_dao = new CourseDAO();
        $course_exists = $course_dao->retrieveByCourseId($bid_input->course);
        if ($course_exists == null){
            $errors[] = "invalid course";
            $course_and_section_valid = False;
        }
        else {
            $section_dao = new SectionDAO();
            $section_exists = $section_dao->retrieveBySection($bid_input);
            if ($section_exists == null){
                $errors[] = "invalid section";
                $course_and_section_valid = False;
            }
        }

        // performs 2 validation checks (but only if course + section combi is valid)
        // firstly, if the student has already completed the course they're trying to bid
        // if not completed, check if the student is eligible to bid in terms of prerequisite completions
        if ($course_and_section_valid == True) {
            $course_completed_dao = new CourseCompletedDAO();
            $student_completed_course = $course_completed_dao->completed_course($bid_input->userid, $bid_input->course);
            if ($student_completed_course) {
                $errors[] = "course completed";
            }
            else {
                $prerequisite_dao = new PrerequisiteDAO();
                $prerequisite = $prerequisite_dao->retrievePrerequisite($bid_input->course);
                if ($prerequisite != null){
                    $course_completed = $course_completed_dao->retrieve($bid_input->userid) ;
                    if (count($prerequisite) != count($course_completed)) {
                        $errors[] = "incomplete prerequisites";
                    }
                }
            }
        }
        
        $student_current_bids = $this->retrieveByUser($bid_input->userid);
        if (count($student_current_bids) >= 5){
            $errors[] = "section limit reached";
        }

        if ($course_and_section_valid == True) {
            // retrieve all the sections the student has bidded for
            $bidding_section = $section_dao->retrieveBySection($bid_input);
            $array_of_bidded_sections = [];
            if (count($student_current_bids) > 0) {
                foreach ($student_current_bids as $bid) {
                    $array_of_bidded_sections[] = $section_dao->retrieveBySection($bid);
                }
            }
            // check for class timetable clash
            $class_time_clash = False;
            foreach ($array_of_bidded_sections as $bidded_section) {
                if ($bidded_section->day == $bidding_section->day and $bidded_section->start == $bidding_section->start and $bidded_section->end == $bidding_section->end) {
                    $class_time_clash = True;
                    break;
                }
            }
            if ($class_time_clash) {
                $errors[] = "class timetable clash";
            }

            // retrieve all the courses the student has bidded for
            $bidding_course = $course_dao->retrieveByCourseId($bid_input->course);
            $array_of_bidded_courses = [];
            if (count($student_current_bids) > 0) {
                foreach ($student_current_bids as $bid) {
                    $array_of_bidded_courses[] = $course_dao->retrieveByCourseId($bid->course);
                }
            }
            // check for exam timetable clash
            $exam_time_clash = False;
            foreach ($array_of_bidded_courses as $bidded_course) {
                if ($bidded_course->exam_date == $bidding_course->exam_date and $bidded_course->exam_start == $bidding_course->exam_start and $bidded_course->exam_end == $bidding_course->exam_end) {
                    $exam_time_clash = True;
                    break;
                }
            }
            if ($exam_time_clash) {
                $errors[] = "exam timetable clash";
            }
        }

        if (!empty($errors)){
            return $errors;
        }

        // update student's edollar
        $to_refund = 0;
        $amount_old = $this->checkExistingBid($bid_input);
        if($amount_old != 0){
            $to_refund = $amount_old - ($bid_input->amount);
            $student_dao->addEdollar($bid_input->userid, $to_refund);
            $sql = 'UPDATE bid SET amount=:amount WHERE userid=:userid AND course=:course AND section=:section' ;
        }
        else{
            $student_dao->deductEdollar($bid_input->userid, $bid_input->amount);
            $sql = 'INSERT IGNORE into bid(userid, amount, course, section) values (:userid, :amount, :course, :section)';
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
        //this takes in a userid , course and section
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