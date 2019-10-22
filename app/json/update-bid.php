<?php
require_once '../include/common.php';
// require_once '../include/protect_json.php';

$input = JSON_DECODE($_REQUEST['r'], true);
// var_dump($input);

$errors = [ isMissingOrEmpty ($input, 'amount'),
            isMissingOrEmpty ($input, 'course'),
            isMissingOrEmpty ($input, 'section'),
            isMissingOrEmpty ($input, 'userid')
            ];
$errors = array_filter($errors);

if (!isEmpty($errors)) {
    $result = [
        "status" => "error",
        "message" => array_values($errors)
        ];
}

else {
    $userid = $input['userid'];
    $amount = $input['amount'];
    $course = $input['course'];
    $section = $input['section'];

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
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
 
?>