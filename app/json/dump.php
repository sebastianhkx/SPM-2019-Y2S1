<?php

require_once '../include/common.php';
// require_once '../include/protect.php';

$bid_dao = new BidDAO();
$course_dao = new CourseDAO();
$course_completed_dao = new CourseCompletedDAO();
$course_enrolled_dao = new CourseEnrolledDAO();
$prerequisite_dao = new PrerequisiteDAO();
$result_dao = new ResultDAO();
$round_status_dao = new RoundStatusDAO();
$section_dao = new SectionDAO();
$student_dao = new StudentDAO();

// var_dump($course_dao->retrieveAll());

// if ( $student != null ) { 
    $result = ["status" => "success", 
                "course" => $course_dao->retrieveAll(),
                "section" => $section_dao->retrieveAll(),
                "student" => $student_dao->retrieveAll(),
                // "prerequisite" => $prerequisite_dao->retrieveAll(),
                "completed-course" => $course_completed_dao->retrieveAll()
            ];
// } 

// else {
//     $result = ["status" => "error"];
// }

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
 
?>