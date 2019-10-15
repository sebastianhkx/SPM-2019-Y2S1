<?php

function roundOneClearing(){
    $bidDAO = new BidDAO();
    $courseSections = $bidDAO->retrieveBiddedSections();
    foreach ($courseSections as $courseSection){
        roundOneResolve($courseSection);
    }
    //clears bid table after clearing
    $bidDAO->deleteAll();
}

function roundOneResolve($courseSection){
    /*
        This function takes in an array [course, section] and resolves the bid according to round 1 logic
        if num bids < class vacancy, all succeed
        if num bid > class vacancy and >1 bid at clearing price, all bids above clearing price succeeds
        if num bids > class vacancy and only 1 bid at clearing price, all bids equals to and above clearing price succeeds
        This function updates the bid_results table and course_enrolled table and clears all bids for the course section
    */
    $bidDAO = new BidDAO();
    $resultDAO = new ResultDAO();
    $studentDAO = new StudentDAO();
    $sectionDAO = new SectionDAO();
    $courseDAO = new CourseDAO();
    $courseEnrolledDAO = new CourseEnrolledDAO();
    $bidObjs = $bidDAO->retrieveByCourseSection($courseSection);
    $sectionObj = $sectionDAO->retrieveBySection($bidObjs[0]);

    $successBids = [];
    $failureBids = [];
    $clearingPriceBids = [];

    $vacancy = $sectionObj->size;//uses size as no student is enrolled in a course in round 1
    if (sizeof($bidObjs)<$vacancy){//all bids succeed
        foreach ($bidObjs as $bidObj){
            //adds all bids to success
            $successBids[] = $bidObj;
        }
    }
    else{
        $clearingPrice = $bidDAO->getClearingPrice($bidObjs[0], $vacancy-1);//vacancy-1 as index starts from 0
        foreach ($bidObjs as $bidObj){
            if ($bidObj->amount==$clearingPrice){
                $clearingPriceBids[] = $bidObj;
            }
        }
        if (sizeof($clearingPriceBids)==1){
            foreach ($bidObjs as $bidObj){
                if ($bidObj->amount >= $clearingPrice){//get success bids
                    $successBids[] = $bidObj;
                }
                else{//get failure bids
                    $failureBids[] = $bidObj;
                }        
            }
        }
        else{
            foreach ($bidObjs as $bidObj){
                if ($bidObj->amount > $clearingPrice){//get success bids
                    $successBids[] = $bidObj;
                }
                else{//get failure bids
                    $failureBids[] = $bidObj;
                }        
            }
        }
    }

    foreach ($successBids as $successBid){
        $resultObj = new Result($successBid->userid, $successBid->amount, $successBid->course, $successBid->section, 'success', 1);
        $sectionObj = $sectionDAO->retrieveBySection($successBid);
        $courseObj = $courseDAO->retrieveByCourseId($successBid->course);
        $courseEnrolledObj = new CourseEnrolled($successBid->userid, $successBid->course, $successBid->section, $sectionObj->day, $sectionObj->start, $sectionObj->end, $courseObj->exam_date, $courseObj->exam_start, $courseObj->exam_end);
        //add to bidresults
        $resultDAO->add($resultObj);
        //add to course_enrolled
        $courseEnrolledDAO->add($courseEnrolledObj);
        //delete from bid table, commented out because current drop bid method refunds
        //$bidDAO->drop($successBid);
    }
    foreach ($failureBids as $failureBid){
        $resultObj = new Result($failureBid->userid, $failureBid->amount, $failureBid->course, $failureBid->section, 'fail', 1);
        //add to bidresult
        $resultDAO->add($resultObj);
        //refund edollars, commented out because drop bid method refunds
        //$studentDAO->addEdollar($failureBid->userid, $failureBid->amount);
        //delete from bid table
        $bidDAO->drop($failureBid);
    }
    roundTwoBidInfo();
}

function roundTwoBidInfo(){
    $sectionDAO = new SectionDAO();
    $bidDAO = new BidDAO();
    $courseEnrolledDAO = new CourseEnrolledDAO();
    $resultDAO = new ResultDAO();
    //empty table
    $resultDAO->deleteInfo();
    //get all course and section
    $courseSections = $sectionDAO->retrieveAll();
    foreach($courseSections as $courseSection){
        $info = $courseEnrolledDAO->retrieveBycourseSection([$courseSection->course,$courseSection->section]);
        //var_dump($info);
        $size = $courseSection->size;
        if($info != null){
            $size = $courseSection->size - sizeof($info);
        }
        $result_info = [$courseSection->course,$courseSection->section,10,$size];
        $bidDAO->addbidinfo($result_info);
    }
}

function roundTwoClearing(){
    $bidDAO = new BidDAO();
    $courseSections = $bidDAO->retrieveBiddedSections();
    foreach ($courseSections as $courseSection){
        roundTwoResolve($courseSection);
    }
    $bidDAO->deleteAll();
}

function roundTwoResolve($courseSection){
    $bidDAO = new BidDAO();
    $resultDAO = new ResultDAO();
    $studentDAO = new StudentDAO();
    $courseDAO = new courseDAO();
    $sectionDAO = new SectionDAO();
    $courseEnrolledDAO = new CourseEnrolledDAO();

    $sectionObj = $sectionDAO->retrieveBySection($bidObjs[0]);
    $bidObjs = $bidDAO->retrieveByCourseSection($courseSection); //retrieves all bids for the course, section
    $courseEnrolledObjs = $courseEnrolledDAO->retrieveByCourseSection($courseSection); //retrieves all courseEnrolled for the course,section
    $size = $sectionObj->size;

    $vacancy = $size - sizeof($courseEnrolledObjs);
    $successfull_price = $bidDAO->getRoundTwoSuccessfullPrice($bidObj[0], $vacancy);

    $success_bids = [];
    $fail_bids = [];

    foreach ($bidObjs as $bidObj){
        if ($bidObj->amount>$successfull_price){
            $success_bids[] = $bidObj;
        }
        else{
            $fail_bids[] = $bidObj;
        }
    }

    foreach ($success_bids as $success_bid){
        $resultObj = new Result($success_bid->userid, $success_bid->amount, $success_bid->course, $success_bid->section, 'success', 2);
        $sectionObj = $sectionDAO->retrieveBySection($success_bid);
        $courseObj = $courseDAO->retrieveByCourseId($success_bid->course);
        $courseEnrolledObj = new CourseEnrolled($success_bid->userid, $success_bid->course, $success_bid->section, $sectionObj->day, $sectionObj->start, $sectionObj->end, $courseObj->exam_date, $courseObj->exam_start, $courseObj->exam_end);
        //add to bidresults
        $resultDAO->add($resultObj);
        //add to course_enrolled
        $courseEnrolledDAO->add($courseEnrolledObj);
        //delete from bid table, commmented out because drop bid refunds
        //$bidDAO->drop($success_bid);
    }

    foreach ($fail_bids as $fail_bid){
        $resultObj = new Result($fail_bid->userid, $fail_bid->amount, $fail_bid->course, $fail_bid->section, 'fail', 2);
        //add to bidresult
        $resultDAO->add($resultObj);
        //refund edollars, commented out because drop bid method refunds
        //$studentDAO->addEdollar($fail_bid->userid, $fail_bid->amount);
        //delete from bid table
        $bidDAO->drop($fail_bid);
    }

}
?>
