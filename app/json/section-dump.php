<?php

require_once '../include/common.php';
require_once '../include/protect_json.php';


$input = JSON_DECODE($_REQUEST['r'], true);


$errors = [ isMissingOrEmptyJson ($input, 'course'),
            isMissingOrEmptyJson ($input, 'section') 
        ];
$errors = array_filter($errors);

$result = [];

if (!isEmpty($errors)) {
    //has common errors
    $result = [
        "status" => "error",
        "messages" => array_values($errors)
        ];
}
else{
    //enters if course section input passes common validation
    $course = $input['course'];
    $section = $input['section'];
    $courseEnrolledDAO = new CourseEnrolledDAO();
    $enrolledObjs = $courseEnrolledDAO->retrieveByCourseSection([$course, $section]);

    //input validation i.e. invalid course/section
    $errors = [];
    $courseDAO = new CourseDAO();
    if ($courseDAO->retrieveByCourseId($course)==null){
        $errors[] = 'invalid course';
    }
    else{
        $sectionDAO = new SectionDAO();
        if ($sectionDAO->retrieveSection($course, $section)==null){
            $errors = 'invalid section';
        }
    }
    if (!empty($errors)){
        //fails input validation
        $result = [
            'status' => 'error',
            'messages' => array_values($errors)
        ];
    }
    else{
        //passes input validation
        $students = [];
        foreach ($enrolledObjs as $enrolledObj){
            $amount = $enrolledObj->amount;
            //adds decimal to amount if amount is not in float form
            if (sizeof(explode('.', $amount))==1){
                $amount .= ".0";
            }
            //floatval converts amount to float
            $students[] = ["userid"=>$enrolledObj->userid, "amount"=>floatval($amount)];
        }
        $result = ['status'=>'success', 'students'=>$students];
    }
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRESERVE_ZERO_FRACTION);
 
?>