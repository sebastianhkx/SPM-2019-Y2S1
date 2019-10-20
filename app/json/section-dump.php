<?php

require_once '../include/common.php';
// require_once '../include/protect_json.php';

// isMissingOrEmpty(...) is in common.php
// $errors = [ isMissingOrEmpty ('course'),
//             isMissingOrEmpty ('section') 
//         ];
// $errors = array_filter($errors);


// if (!isEmpty($errors)) {
//     $result = [
//         "status" => "error",
//         "messages" => array_values($errors)
//         ];
// }
// else{
    $course = $_REQUEST['course'];
    $section = $_REQUEST['section'];

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

    if ( empty($invalid_errors) ) { 
        $result = ["status" => "success", 
                    // "students" => $course_enrolled_dao->retrieveByCourseSection([$course, $section])[0]
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