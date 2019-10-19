<?php
require_once '../include/common.php';
require_once '../include/protect_json.php';

$errors = [ isMissingOrEmpty ('userid'),
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
    $bid_dao = new BidDAO();

    $userid = $_REQUEST['userid'];
    $course = $_REQUEST['course'];
    $section = $_REQUEST['section'];

    $bid_to_drop_temp = new Bid($userid, 0, $course, $section); // the current drop bid method doesn't need amount. might need to revisit the method.
    $bid_to_drop = new Bid($userid, $bid_dao->checkExistingBid($bid_to_drop_temp), $course, $section);
    $drop_bid = $bid_dao->drop($bid_to_drop);

    if (is_array($drop_bid)) { 
        $result = [
            "status"=>"error",
            "message" => $drop_bid
        ];
    }
    
    else {
        $result = [
            "status" => "success"
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
 
?>