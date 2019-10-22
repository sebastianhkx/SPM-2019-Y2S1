<?php
require_once '../include/common.php';
// require_once '../include/protect_json.php';

$input = JSON_DECODE($_REQUEST['r'],TRUE);

$errors = [ 
            isMissingOrEmptyJson ($input, 'course'),
            isMissingOrEmptyJson ($input, 'userid'),
            isMissingOrEmptyJson ($input, 'section')];
            
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