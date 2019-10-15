<?php
require_once '../include/common.php';
// require_once '../include/token.php';
// require_once '../include/protect.php';

$errors = [ isMissingOrEmpty ('userid'),
            isMissingOrEmpty ('amount'),
            isMissingOrEmpty ('course'),
            isMissingOrEmpty ('section')];
$errors = array_filter($errors);

if (!isEmpty($errors)) {
    $result = [
        "status" => "error",
        "messages" => array_values($errors)
        ];
}

else {
    $userid = $_REQUEST['userid'];
    $amount = $_REQUEST['amount'];
    $course = $_REQUEST['course'];
    $section = $_REQUEST['section'];
    // $userid = 'ben.ng.2009';
    // $course = 'IS108';
    // $section = 'S1';
    // $amount = '20';

    $bid_dao = new BidDAO();
    $bidded = new Bid($userid, $amount, $course, $section);
    $update_bid = $bid_dao->add($bidded);

    if (is_array($update_bid)) { 
        $result = [
            "status"=>"error",
            "message" => $update_bid
        ];
    }
    
    else {
        $result = [
            "status" => "success"
        ];
    }

        // if ( $update_bid == true ) { 
        //     $result = [
        //         "status"=>"success"
        //     ];
        // }
        
        // else {
        //     $result = [
        //         "status" => "error", 
        //         "message" => $update_bid
        //     ];
        // }
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
 
?>