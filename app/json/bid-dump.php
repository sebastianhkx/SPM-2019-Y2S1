<?php

require_once '../include/common.php';
// require_once '../include/protect_json.php';

// isMissingOrEmpty(...) is in common.php
// $errors = [ isMissingOrEmpty ('course'),
//             isMissingOrEmpty ('section') 
//         ];
// $errors = array_filter($errors);


// if (!isEmpty($errors)) {
//     $result = [
//         "status" => "error",
//         "messages" => array_values($errors)
//         ];
// }
// else{
    // $course = $_REQUEST['course'];
    // $section = $_REQUEST['section'];

    // // invalid course/section validation
    // $invalid_errors = [];

    // $courseDAO = new CourseDAO();
    // $sectionDAO = new SectionDAO();
    // if (($courseDAO->retrieveByCourseId($course))==null){
    //     $invalid_errors[] = "invalid course";
    // }
    // else{
    //     if (($sectionDAO->retrieveSection($course, $section)==null)){
    //         $invalid_errors[] = "invalid section";
    //     } 
    // }

    $bid_dao = new BidDAO();
    $round_status_dao = new RoundStatusDAO();
    $result_dao = new ResultDAO();


    $bidDisplay=[];
    $round_status=$round_status_dao->retrieveall();
    foreach($round_status as $one_status){
        if(($one_status->round_num='1' && $one_status->status=="started") || ($one_status->round_num='2' && $one_status->status=="started")){
            $bids=$bid_dao->retrieveAll();
            for ($i=1; $i <count($bids) ; $i++) { 
                $one_bid=$bids[$i];
                $bidDisplay[]=[
                    "row"=>$i,
                    "userid"=>$one_bid->getUserid(),
                    "amount"=>$one_bid->getAmountJSON(),
                    "result"=>'-'
                ];
            }
        }
        else{
            foreach($result_dao->retrieveall() as $one_result){
                $bids=$bid_dao->retrieveAll();
                for ($i=1; $i <count($bids) ; $i++) { 
                    $one_bid=$bids[$i];
                    $bidDisplay[]=[
                        "row"=>$i,
                        "userid"=>$one_bid->getUserid(),
                        "amount"=>$one_bid->getAmountJSON(),
                        "result"=>$one_bid->getResult()
                    ];
                }
            }
        }
    }

    foreach($bidDisplay as $key=>$value){
        $amount[$key] = $value['amount'];
        $userid[$key] = $value['userid'];
    }
    array_multisort($amount, SORT_DESC, $userid, SORT_ASC, $bidDisplay);
    if ( empty($invalid_errors) ) { 
        $result = ["status" => "success", 
                    "bids"  => $bidDisplay
                ];
    } 
    else {
        $result = ["status" => "error", 
                    "messages" => $invalid_errors
                ];
    }
// }

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
 
?>