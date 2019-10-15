<?php

require_once '../include/common.php';
require_once '../include/token.php';
// require_once '../include/protect.php';

$bid_dao = new BidDAO();
$userid = $_SESSION['userid'];

if (isset($_POST['submitbid'])) {
    $course = $_POST['course'];
    $section = $_POST['section'];
    $amount = $_POST['bidamount'];
}

$bidded = new Bid($userid, $amount, $course, $section);
$update_bid = $bid_dao->add($bidded);

    if ( $update_bid == true ) { 
        $result = [
            "status"=>"success"
        ];
    } 
    
    else {
        $result = [
            "status" => "error", 
            "message" => $update_bid
        ];
    }

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
 
?>