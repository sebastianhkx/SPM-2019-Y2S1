<?php

require_once '../include/common.php';
// require_once '../include/protect_json.php';


$assoc = TRUE;

$jsonStr = $_REQUEST['r'];
$arr= array(
    json_decode($jsonStr, $assoc)['course'],
    json_decode($jsonStr, $assoc)['section']
);
// isMissingOrEmpty(...) is in common.php
$errors = [ isMissingOrEmpty ('course'),
            isMissingOrEmpty ('section') 
        ];
$errors = array_filter($errors);


// if (!isEmpty($errors)) {
//     $result = [
//         "status" => "error",
//         "messages" => array_values($errors)
//         ];
// }
// else{
    $courseSection = $arr;
    $course=$courseSection[0];
    $section=$courseSection[1];

    // invalid course/section validation
    $invalid_errors = [];

    $courseDAO = new CourseDAO();
    $sectionDAO = new SectionDAO();
    if (($courseDAO->retrieveByCourseId($course))==null){
        $invalid_errors[] = "invalid course";
    }
    else{
        if (($sectionDAO->retrieveSection($course, $section)==null)){
            $invalid_errors[] = "invalid section";
        } 
    }

    $course_enrolled_dao = new CourseEnrolledDAO();
    $course_enrolled=$course_enrolled_dao->retrieveByCourseSection($courseSection);
    $studentDisplay=[];
    foreach($course_enrolled as $one_course_enrolled){
        $studentDisplay[]=[
            "userid"=>$one_course_enrolled->getUserid(),
            "amount"=>$one_course_enrolled->getAmountJSON()
        ];
    }
    asort($studentDisplay);
    if ( empty($invalid_errors) ) { 
        $result = ["status" => "success", 
                   "students" =>$studentDisplay
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