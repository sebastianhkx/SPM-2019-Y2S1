<?php
require_once '../include/common.php';
require_once '../include/protect_json.php';

$errors = [ 
            isMissingOrEmpty ('course'),
            isMissingOrEmpty ('userid'),
            isMissingOrEmpty ('section')];
            
$errors = array_filter($errors);

if (!isEmpty($errors)) {
    $result = [
        "status" => "error",
        "message" => array_values($errors)
        ];
}

else {
    $userid = $_REQUEST['userid'];
    $course = $_REQUEST['course'];
    $section = $_REQUEST['section'];

    $r2bid_dao = new R2BidDAO();
    $courseEnrolled_dao = new CourseEnrolledDAO;
    // $courseEnrolled = $courseEnrolled_dao->retrieveByUseridCourse($userid, $course);
    $drop_section = $r2bid_dao->r2dropSection($course,$userid);

    if (is_array($drop_bid)) { 
        $result = [
            "status"=>"error",
            "message" => $drop_section
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