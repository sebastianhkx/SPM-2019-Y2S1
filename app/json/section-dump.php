<?php

require_once '../include/common.php';
// require_once '../include/protect.php';


// isMissingOrEmpty(...) is in common.php
$errors = [ isMissingOrEmpty ('userid') ];
$errors = array_filter($errors);


if (!isEmpty($errors)) {
    $result = [
        "status" => "error",
        "messages" => array_values($errors)
        ];
}
else{
    $userid = $_REQUEST['userid'];

    $student_dao = new StudentDAO();
    $student = $student_dao->retrieve($userid);

    if ( $student != null ) { 
        $result = ["status" => "success", 
                    "userid" => $student->userid,
                    "password" => $student->password,
                    "name" => $student->name,
                    "school" => $student->school,
                    "edollar" => $student->edollar
                ];
    } 
    else {
        $result = ["status" => "error", 
                    "messages" => ['invalid userid']
                ];
    }
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
 
?>