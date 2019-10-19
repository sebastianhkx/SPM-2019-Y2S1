<?php
require_once '../include/common.php';
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
    
    
    if (is_array($update_bid) || ) { 

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