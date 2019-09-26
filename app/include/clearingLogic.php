<?php

function roundOneClearing(){
    $bidDAO = new BidDAO();
    $courseSections = $bidDAO->retrieveBiddedSections();
    foreach ($courseSections as $courseSection){
        roundOneResolve($courseSection);
    }
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
    $courseDAO = new CouseDAO();
    $courseEnrolledDAO = new CourseEnrolledDAO();
    
    $bidObjs = $bidDAO->retrieveByCourseSection($courseSection);
    $sectionObj = $sectionDAO->retrieve($bidObjs[0]);

    $successBids = [];
    $failureBids = [];
    $clearingPriceBids = [];

    $vacancy = $sectionObj->size();//uses size as no student is enrolled in a course in round 1
    if (sizeof($bidObjs)<$vacancy){//all bids succeed
        foreach ($bidObjs as $bidObj){
            //adds all bids to success
            $successBids[] = $bidObj;
        }
    }
    else{
        $clearingPrice = getClearingPrice($bidObjs[0], $vacancy-1);//vacancy-1 as index starts from 0
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
        $resultObj = new Result($successBid->userid, $successBid->amount, $successBid->section, 'success', 1);
        $sectionObj = $sectionDAO->retrieveBySection($successBid);
        $courseObj = $courseDAO->retrieveByCourseId($successBid->course)
        $courseEnrolledObj = new CourseEnroll($successBid->userid, $successBid->course, $successBid->section, $sectionObj->day, $sectionObj->start, $sectionObj->end, $courseObj->exam_date, $courseObj->exam_start, $courseObj->exam_end);
        //add to bidresults
        $resultDAO->add($resultObj);
        //add to course_enrolled
        $courseEnrolledDAO->add($courseEnrolledObj);
        //delete from bid table
        $bidDAO->drop($successBid);
    }
    foreach ($failureBids as $failureBid){
        $resultObj = new Result($successBid->userid, $successBid->amount, $successBid->section, 'fail', 1);
        //add to bidresult
        $resultDAO->add($resultObj)
        //refund edollars
        $student->addEdollar($failureBid->userid, $failureBid->amount);
        //delete from bid table
        $bidDAO->drop($failureBid);
    }
}

?>