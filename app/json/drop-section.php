<?php
require_once '../include/common.php';
require_once '../include/protect_json.php';

$input = [];
if (isset($_REQUEST['r'])){
    $input = JSON_DECODE($_REQUEST['r'], true);
}

$errors = [ 
            isMissingOrEmptyJson ($input, 'course'),
            isMissingOrEmptyJson ($input, 'section'),
            isMissingOrEmptyJson ($input, 'userid')
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
    $course = $input['course'];
    $section = $input['section'];

    $r2bid_dao = new R2BidDAO();
    $courseEnrolled_dao = new CourseEnrolledDAO;
    // $courseEnrolled = $courseEnrolled_dao->retrieveByUseridCourse($userid, $course);
    $drop_bid = $r2bid_dao->r2dropSection($userid,$course,$section);

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