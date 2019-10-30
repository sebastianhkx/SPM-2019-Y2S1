<?php

require_once '../include/common.php';
## testing comment out
require_once '../include/protect_json.php';

// isMissingOrEmpty(...) is in common.php

$input = [];
if (isset($_REQUEST['r'])){
    $input = JSON_DECODE($_REQUEST['r'], true);
}

$errors = [ isMissingOrEmptyJson ($input, 'userid') ];
$errors = array_filter($errors);



if (!isEmpty($errors)) {
    $result = [
        "status" => "error",
        "message" => array_values($errors)
        ];
}
else{
    $userid = $input['userid'];

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
}

// header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT | JSON_PRESERVE_ZERO_FRACTION);
 
?>