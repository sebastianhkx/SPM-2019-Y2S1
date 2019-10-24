<?php

require_once '../include/common.php';
// require_once '../include/protect_json.php';

// isMissingOrEmpty(...) is in common.php



$assoc = TRUE;

$jsonStr = $_REQUEST['r'];
$arr= array(
    json_decode($jsonStr, $assoc)['course'],
    json_decode($jsonStr, $assoc)['section']
);


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
    
    
    $round_status_dao = new RoundStatusDAO();
    $result_dao = new ResultDAO();

    
    $results = $result_dao->retrieveByCourseSection($courseSection);

    $bidDisplay=[];
    $round_status=$round_status_dao->retrieveall();
    if(($round_status[0]->status=="started") || ($round_status[1]->status=="started")){
        for ($i=1; $i <count($results) ; $i++) { 
            $one_bid=$results[$i];
            $bidDisplay[]=[
                "row"=>$i,
                "userid"=>$one_bid->getUserid(),
                "amount"=>$one_bid->getAmountJSON(),
                "result"=>'-'
            ];
        }
    }
    else{
        for ($i=1; $i <count($results) ; $i++) { 
                $one_bid=$results[$i];
                $bidDisplay[]=[
                    "row"=>$i,
                    "userid"=>$one_bid->getUserid(),
                    "amount"=>$one_bid->getAmountJSON(),
                    "result"=>$one_bid->getResult()
                ];
        }
    }

foreach($bidDisplay as $key=>$value){
    $amount[$key] = $value['amount'];
    $userid[$key] = $value['userid'];
}

array_multisort($amount, SORT_DESC,SORT_NUMERIC, $userid,SORT_ASC,SORT_STRING,$bidDisplay);


if ( empty($invalid_errors) ) { 
    $result = ["status" => "success", 
                "bids"  => $bidDisplay
            ];
} 
else {
    $result = ["status" => "error", 
                "messages" => $invalid_errors
            ];
}
//}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
 
?>