<?php

require_once '../include/common.php';
## testing comment out
// require_once '../include/protect_json.php';

// isMissingOrEmpty(...) is in common.php
$assoc = TRUE;

$jsonStr = $_REQUEST['r'];
$userid = json_decode($jsonStr, $assoc)['userid'];

$errors = [ isMissingOrEmpty ($userid) ];
$errors = array_filter($errors);


// if (!isEmpty($errors)) {
    // $result = [
    //     "status" => "error",
    //     "message" => array_values($errors)
    //     ];
// }
// else{
    // $userid = $_REQUEST['userid'];

    $student_dao = new StudentDAO();
    $student = $student_dao->retrieve($userid);

    if ( $student != null ) { 
        $result = ["status" => "success", 
                    "userid" => $student->userid,
                    "password" => $student->password,
                    "name" => $student->name,
                    "school" => $student->school,
                    "edollar" => floatval($student->getEdollarJSON())
                ];
    } 
    else {
        $result = ["status" => "error", 
                    "message" => ['invalid userid']
                ];
    }
// }

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);
 
?>