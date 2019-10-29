<?php
require_once '../include/common.php';
require_once '../include/protect_json.php';

$input = JSON_DECODE($_REQUEST['r'],true);

$errors = [ isMissingOrEmptyJson ($input, 'course'),
            isMissingOrEmptyJson ($input, 'section'),
            isMissingOrEmptyJson ($input, 'userid')];
$errors = array_filter($errors);

if (!isEmpty($errors)) {
    $result = [
        "status" => "error",
        "message" => array_values($errors)
        ];
}

else {
    $bid_dao = new BidDAO();

    $userid = $input['userid'];
    $course = $input['course'];
    $section = $input['section'];

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