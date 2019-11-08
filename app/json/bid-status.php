<?php

require_once '../include/common.php';
// require_once '../include/protect_json.php';

$input = [];
if (isset($_REQUEST['r'])){
    $input = JSON_DECODE($_REQUEST['r'], true);
}

$errors = [ isMissingOrEmptyJson ($input, 'course'),
            isMissingOrEmptyJson ($input, 'section') 
        ];
$errors = array_filter($errors);

if (!isEmpty($errors)) {
    $result = [
        "status" => "error",
        "message" => array_values($errors)
        ];
}
else{
    //enters if course section input passes common validation
    $course = $input['course'];
    $section = $input['section'];

    //input validation i.e. invalid course/section
    $errors = [];
    $courseDAO = new CourseDAO();
    if ($courseDAO->retrieveByCourseId($course)==null){
        $errors[] = 'invalid course';
    }
    else{
        $sectionDAO = new SectionDAO();
        if ($sectionDAO->retrieveSection($course, $section)==null){
            $errors = 'invalid section';
        }
    }
    if (!empty($errors)){
        $result = [
            'status' => 'error',
            'message' => array_values($errors)
        ];
    }
    else{
        $bidDAO = new BidDAO();
        $roundStatusDAO = new RoundStatusDAO();
        $studentDAO = new StudentDAO();
        $roundStatus = $roundStatusDAO->retrieveAll();
        $current_round = $roundStatusDAO->retrieveCurrentActiveRound();
        $bids_to_ret = [];
        $bids = $bidDAO->retrieveByCourseSection([$course, $section]);
        if ($current_round!==null) { 
            if ($current_round->round_num == 1) { // if active round is 1
                $section_dao = new SectionDAO();
                $vacancy = $section_dao->retrievebyCourseSection($course, $section)->size; // get vacancy
            }
            else {  // if active round is 2
                $bidObj = new Bid('', '', $course, $section);
                $r2_bid_dao = new R2BidDAO();
                $r2_bid_info = $r2_bid_dao->getr2bidinfo($bidObj);
                $vacancy = $r2_bid_info->vacancy;
                $min_bid = $r2_bid_info->min_amount;
            }
            if ($bids != null) {
                $bid_amounts = []; // to find min_bid, different from clearing price
                foreach ($bids as $bidObj){ // get bids in round
                    $amount = $bidObj->amount;
                    $bid_amounts[] = $amount;
                    // adds decimal to amount if amount is not in float form
                    if (sizeof(explode('.', $amount))==1){
                        $amount .= ".0";
                    }
                    if ($current_round->round_num == 2) {
                        $clearingPrice = $bidDAO->getRoundTwoSuccessfullPrice($bidObj, $vacancy);
                        if ($bidObj->amount>$clearingPrice) {
                        $result = 'success';
                        }
                        else {
                        $result = 'fail';
                        }
                    }
                    else {
                        $result = 'pending';
                    }
                    //floatval converts amount to float
                    $bids_to_ret[] = ["userid"=>$bidObj->userid, "amount"=>floatval($amount), "balance"=>$studentDAO->retrieve($bidObj->userid)->edollar, "status"=>$result];
                }
            }
            if ($current_round->round_num == 1) {
                $min_bid = 10; // initialise to 10 if 0 bids
                if ($bids != null) {
                    if (count($bids) < $vacancy) {
                        $min_bid = min($bid_amounts); // if #bids < #vacancy
                    }
                    else {
                        $min_bid = $bidDAO->getClearingPrice($bidObj, $vacancy-1); // if #bids >= #vacancy
                    }
                }
            }
            if (sizeof(explode('.', $min_bid))==1) {
                $min_bid .= ".0";
            }
            $result = ['status' => 'success', 
                        'vacancy' => $vacancy,
                        'min-bid-amount' => floatval($min_bid),
                        'students' => $bids_to_ret
                    ];
        }
        else{
            $resultDAO = new ResultDAO();
            $studentDAO = new StudentDAO();
            $r2_bid_dao = new R2BidDAO();
            $course_enrolled_dao = new CourseEnrolledDAO();
            $bidObj = new Bid('', '', $course, $section);
            $r2_bid_info = $r2_bid_dao->getr2bidinfo($bidObj);
            $vacancy = $sectionDAO->retrievebyCourseSection($course, $section)->size - count($course_enrolled_dao->retrieveByCourseSection([$course, $section]));
            if ($roundStatus[1]->status=='ended') {
                // no active round, last active is round 2
                $results1 = $resultDAO->retrieveByRound(1);
                $results2 = $resultDAO->retrieveByRound(2);
                $results = array_merge($results1, $results2);
            }
            elseif ($roundStatus[0]->status=='ended') {
                // no active round, last active is round 1
                $results = $resultDAO->retrieveByRound(1);
            }
            $min_bid = 10; // initialise to 10 if no results
            $bid_amounts = []; // to get min bid
            if (!empty($results)) {
                foreach ($results as $resultObj) {
                    if ($resultObj->course==$course && $resultObj->section==$section) {
                        $status = $resultObj->result;
                        $amount = $resultObj->amount;
                        if ($status == 'success') {
                            $bid_amounts[] = $amount;
                        }
                        //adds decimal to amount if amount is not in float form
                        if (sizeof(explode('.', $amount))==1) {
                            $amount .= ".0";
                        }

                        if ($roundStatus[0]->status=='ended' && $roundStatus[0]->status=='pending') {
                        $bids_to_ret[] = ["userid"=>$resultObj->userid, "amount"=>floatval($amount), "balance"=>$studentDAO->retrieve($resultObj->userid)->edollar, "status"=>$status];
                        }
                        elseif ($roundStatus[0]->status=='ended' && $roundStatus[0]->status=='ended') {
                            if ($status == 'success') {
                                $bids_to_ret[] = ["userid"=>$resultObj->userid, "amount"=>floatval($amount), "balance"=>$studentDAO->retrieve($resultObj->userid)->edollar, "status"=>$status];
                            }
                        }
                    }
                }
            }
            if (!empty($bid_amounts)) {
                $min_bid = min($bid_amounts);
            }

            if ($roundStatus[1]->status=='ended') {
                if ($results2 == null) {
                    $min_bid = 10;
                }
                else {
                $bid_amounts = [];
                foreach ($results2 as $resultObj) {
                    if ($resultObj->course==$course && $resultObj->section==$section) {
                        $status = $resultObj->result;
                        $amount = $resultObj->amount;
                        if ($status == 'success') {
                            $bid_amounts[] = $amount;
                        }
                    }
                }
                $min_bid = min($bid_amounts);              
                }
            }
            $result = ['status' => 'success', 
                        'vacancy' => $vacancy,
                        'min-bid-amount' => floatval($min_bid),
                        'students' => $bids_to_ret
                    ];
        }
    }  
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);
 
?>