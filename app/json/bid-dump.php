<?php

require_once '../include/common.php';
// require_once '../include/protect_json.php';

// isMissingOrEmpty(...) is in common.php


$input = [];
if (isset($_REQUEST['r'])){
    $input = JSON_DECODE($_REQUEST['r'], true);
}

// var_dump($input);
// var_dump(null===0);

$errors = [ isMissingOrEmptyJson ($input, 'course'),
            isMissingOrEmptyJson ($input, 'section') 
        ];
$errors = array_filter($errors);
// var_dump($errors);

if (!isEmpty($errors)) {
    $result = [
        "status" => "error",
        "messages" => array_values($errors)
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
            'messages' => array_values($errors)
        ];
    }
    else{
        $bidDAO = new BidDAO();
        $roundStatusDAO = new RoundStatusDAO();
        $roundStatus = $roundStatusDAO->retrieveAll();
        $current_round = $roundStatusDAO->retrieveCurrentActiveRound();
        $bids_to_ret = [];
        if ($current_round!==null){
            //has active round
            $bids = $bidDAO->retrieveByCourseSection([$course, $section]);
            $i = 1;
            foreach ($bids as $bidObj){
                $amount = $bidObj->amount;
                //adds decimal to amount if amount is not in float form
                if (sizeof(explode('.', $amount))==1){
                    $amount .= ".0";
                }
                //floatval converts amount to float
                $bids_to_ret[] = ["row"=>$i, "userid"=>$bidObj->userid, "amount"=>floatval($amount), "result"=>'-'];
                $i++;
            }
            $result = ['status'=>'success', 'bids'=>$bids_to_ret];
        }
        else{
            $resultDAO = new ResultDAO();
            $i=1;
            if ($roundStatus[1]->status=='ended'){
                //no active round, last active is round 2
                $results = $resultDAO->retrieveByRound(2);
            }
            elseif ($roundStatus[0]->status=='ended'){
                //no active round, last active is round 1
                $results = $resultDAO->retrieveByRound(1);
            }
            if (!empty($results)){
                foreach ($results as $resultObj){
                    if ($resultObj->course==$course && $resultObj->section==$section){
                        $amount = $resultObj->amount;
                        //adds decimal to amount if amount is not in float form
                        if (sizeof(explode('.', $amount))==1){
                            $amount .= ".0";
                        }
                        if ($resultObj->result=='success'){
                            $bid_result = 'in';
                        }
                        else{
                            $bid_result = 'out';
                        }
                        $bids_to_ret[] = ["row"=>$i, "userid"=>$resultObj->userid, "amount"=>floatval($amount), "result"=>$bid_result];
                        $i++;
                    }
                }
            }
            $result = ['status'=>'success', 'bids'=>$bids_to_ret]; 
        }
    }  
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);
 
?>