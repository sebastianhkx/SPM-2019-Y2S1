<?php
class R2BidDAO{

    public function getr2bidinfo($bidobj){
        //this function take in a bid object and return an object with section's minimum and vacancy
        $sql = 'SELECT * from r2_bid_info where course=:course and section=:section';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $bidobj->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $bidobj->section, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = [];

        if ($row = $stmt->fetch()){
            $result = new R2Bid($row['course'],$row['section'],$row['min_amount'],$row['vacancy']);
            //$result = array("course"=>$row["course"], "section"=>$row['section'], "min_amount"=>$row["min_amount"],"vacancy"=>$row['vacancy']);
        }
        return $result;
    }

    public function addbidinfo($r2Bid){
        // this function take in an object for R2 bid info 
        $sql = "INSERT IGNORE INTO r2_bid_info(course, section,min_amount,vacancy) VALUES (:course, :section, :min_amount,:vacancy)";

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $r2Bid->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $r2Bid->section, PDO::PARAM_STR);
        $stmt->bindParam(':min_amount', $r2Bid->min_amount, PDO::PARAM_STR);
        $stmt->bindParam(':vacancy', $r2Bid->vacancy, PDO::PARAM_INT);



        $isAddOk = FALSE;
        if ($stmt->execute()){
            $isAddOk = TRUE;
        }
        
        return $isAddOk;
    }

    public function getBid($prv_clearingprice,$r2bid){
        // this function take in a bid object and a clearing price to get the total number of bids that more than or equal to the clearing price
        // return a number
        $sql = 'SELECT count(userid) as num from bid where course=:course and section=:section and amount >= :amount';

        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $r2bid->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $r2bid->section, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $prv_clearingprice, PDO::PARAM_STR);

        $result = 0;
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        if($row = $stmt->fetch()){
            $result = $row['num'];
        }

        return $result;
    }

    public function getminimunprice($bidobj){
        //this function take in a bid object and return the minimun amount for that section 
        $sql = 'SELECT min_amount from r2_bid_info where course=:course and section=:section order by min_amount DESC';
        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $bidobj->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $bidobj->section, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $result = 10;

        if ($row = $stmt->fetch()){
            $result = $row['min_amount'];
        }
        return $result;
    }

    public function updateBidinfo($r2Bid_info){

        //var_dump($result);
        $sql = 'UPDATE r2_bid_info SET min_amount = :min_amount  WHERE course=:course AND section = :section';
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $r2Bid_info->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $r2Bid_info->section, PDO::PARAM_STR);
        $stmt->bindParam(':min_amount', $r2Bid_info->min_amount, PDO::PARAM_STR);

        $output = $stmt->execute();

        // var_dump($output);

        return $output;
    }

    public function updateBidVacancy($r2Bid_info){
        //var_dump($result);
        $sql = 'UPDATE r2_bid_info SET vacancy = :vacancy  WHERE course=:course AND section = :section';
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $r2Bid_info->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $r2Bid_info->section, PDO::PARAM_STR);
        $stmt->bindParam(':vacancy', $r2Bid_info->vacancy, PDO::PARAM_INT);

        $output = $stmt->execute();

        // var_dump($output);

        return $output;
    }

    public function deleteInfo(){
        //this function is used to empty the r2_bid_info table
        $sql = 'TRUNCATE TABLE r2_bid_info';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $count = $stmt->rowCount();

        $stmt = null;
        $conn = null; 
    }

    public function r2dropSection($userid,$drop_course,$drop_section){
        //this function take in a section object to reset the vacancy for that section
        $errors = null;

        //course code not exist
        $course_dao = new CourseDAO();
        $course = $course_dao->retrieveByCourseId($drop_course);
        if($course == null){
            $errors[] = "invalid course";
        }
        else{
            //check section exist
            $section_dao = new SectionDAO();
            $section = $section_dao -> retrieveSection($drop_course,$drop_section);
            if($section == null){
                $errors[] = "invalid section";
            }
        
        }
        //check userid exist
        $student_dao = new StudentDAO();
        $student = $student_dao->retrieve($userid);
        if(empty($student)){
            $errors[] = "invalid userid";
        }

        //check if round active
        $round_dao = new RoundStatusDAO();
        $round = $round_dao->retrieveCurrentActiveRound();
        if($round == null){
            $errors[] = 'round not active';
        }

        if(!empty($errors)){
            return $errors;
        }

        // if ($course_enrolled==null){
        //     return True;
        // }
        $courseEnrolled_dao = new CourseEnrolledDAO();
        $result_dao = new ResultDAO();
        $student_dao = new StudentDAO();
        $course_enrolled = $courseEnrolled_dao -> retrieveByUseridCourse($userid,$drop_course);
        $courseEnrolled_dao -> delete($course_enrolled);
        $result = $result_dao->retrieveByCourseEnrolled($course_enrolled);
        $result_dao->delete($result);
        $student_dao->addEdollar($result->userid, $result->amount);


        $sql = 'UPDATE r2_bid_info SET vacancy = vacancy + 1 WHERE course=:course AND section = :section';
        $connMgr = new ConnectionManager();
        $conn = $connMgr->getConnection();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course', $sectionobj->course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $sectionobj->section, PDO::PARAM_STR);

        $isOK = FALSE;
        if($stmt->execute()){
            $isOK = TRUE;
        }
        $stmt = null;
        $conn = null; 

        return $isOK ;
    }
    
    // function searchCourseSection($bid){
    //     //this function takes in a bid object to check if course and section exists
    //     //return an index array incluing errors and an associative array of 
    //     $section_dao = new SectionDAO();
    //     $courseEnrolled_dao = new CourseEnrolledDAO(); 
    //     $section = $section_dao->retrieveBySection($bid);
    //     $result = [];
    //     $errors = null;
    //     if($section == null){
    //         $errors[] = 'invalid course/section';
    //     }
        
    //     if($errors == null){
    //         $courseEnrolledObjs = $courseEnrolled_dao->retrieveByCourseSection([$bid->course, $bid->section]);
    //         //var_dump($courseEnrolledObjs);
    //         if ($courseEnrolledObjs != null){
    //             $enrolled = sizeof($courseEnrolledObjs);
    //         }
    //         else{
    //             $enrolled = 0;
    //         }
    //         $size = $section_dao->retrieveBySection($bid)->size;
    //         if ($size-$enrolled<=0){
    //             $errors[] = 'no vacancy';
    //         }
    //     }
  
    //     if($errors == null){
    //         $result = $this->updateBidinfo($bid);
    //     }
        
    //     return [$result,$errors];
    // }

    // function checkBidsStatus($bidsobj){
    //     //this function take in an array of bid object and return an array of bid object and bid status
    //     $result = [];

    //     foreach($bidsobj as $bid){
    //         $state = "Successful";
    //         $bid_info = $this->updateBidinfo($bid);
    //         if(!in_array(array($bid->amount,'Successful'),$bid_info)){
    //             $state = "Unsuccessful";
    //         }
    //         $result[] = array('bid'=>$bid,'status'=>$state);
    //     }
    //     return $result;
    // }

    // function checkCourseEnrolled($bid){
    //     //this function take in a bid object to check number of course enrolled
    //     $courseEnrolled_dao =  new CourseEnrolledDAO;
    //     $bid_dao = new BidDAO();
    //     $course_dao = new CourseDAO();
    //     $section_dao = new SectionDAO();

    //     $coursesEnrolled = $courseEnrolled_dao -> retrieveByUserid($bid->userid);
    //     $bids = $bid_dao->retrieveByUser($bid->userid);
    //     $errors = [];
    //     $availablebid = 5 - sizeof($coursesEnrolled);

    //     if(sizeof($coursesEnrolled) == 5){
    //         $errors[] = 'more than 5 courses enrolled';
    //     }

    //     if(empty($errors) && $availablebid == sizeof($bids)){
    //         $errors[] = 'section limit reached';
    //     }

    //     //check class time table and exam timetable for current bid and course enrolled
    //     //exam clash
    //     $course_bid = $course_dao->retrieveByCourseId($bid->course);
    //     $new_start = $course_bid->exam_start;
    //     $new_end = $course_bid->exam_end;
    //     foreach ($coursesEnrolled as $bidObj){
    //         $existingBidCourseObj = $course_dao->retrieveByCourseId($bidObj->course);
    //         $existingStart = $existingBidCourseObj->exam_start;
    //         $existingEnd = $existingBidCourseObj->exam_end;
    //         //1st condition checks that the course for the new bid and existing bid doesnt match, because new bid updates old bid and exam clash wouldnt matter
    //         //2nd condition checks if exam date clash, 3rd condition checks if new start between existing start end, 4th checks if existing start between new start end, 5th checks if either start end overlaps
    //         if ($bidObj->course != $bid->course && $course_bid->exam_date == $existingBidCourseObj->exam_date && (($new_start<$existingEnd and $new_start>$existingStart) || ($existingStart<$new_end and $existingStart>$new_start) || ($new_start == $existingStart || $new_end == $existingEnd))){
    //             $errors[] = 'exam timetable clash';
    //             break;
    //         }
    //     }

    //     //class clash
    //     $section_bid = $section_dao->retrieveBySection($bid);
    //     $new_start = $section_bid->start;
    //     $new_end = $section_bid->end;
    //     foreach ($coursesEnrolled as $bidObj){
    //         $existingBidSectionObj = $section_dao->retrieveBySection($bidObj);//existing bid
    //         $existingStart = $existingBidSectionObj->start;
    //         $existingEnd = $existingBidSectionObj->end;
    //         if ($bidObj->course != $bid->course && $bidSectionObj->day == $existingBidSectionObj->day && (($new_start<$existingEnd and $new_start>$existingStart) || ($existingStart<$new_end and $existingStart>$new_start) || ($new_start == $existingStart || $new_end == $existingEnd)))
    //             //1st condition checks that the course for the new bid and existing bid doesnt match, because new bid updates old bid and timetable clash wouldnt matter
    //             //2nd condition checks if days clash, 3rd condition checks if new start between existing start end, 4th checks if existing start between new start end, 5th checks if either start end overlaps
    //             $errors[] = 'class timetable clash';
    //             break;
    //     }

    //     if (empty($errors)){
    //         $errors = $bid_dao->add($bid);
    //     }

    //     return $errors;
    // }

    function deleteAll(){
        $sql = 'TRUNCATE TABLE r2_bid_info';

        $connMgr = new ConnectionManager();      
        $conn = $connMgr->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();
        $count = $stmt->rowCount();

        $stmt = null;
        $conn = null; 
    }
}
