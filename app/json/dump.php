<?php

require_once '../include/common.php';
// require_once '../include/protect_json.php';

$bid_dao = new BidDAO();
$course_dao = new CourseDAO();
$course_completed_dao = new CourseCompletedDAO();
$course_enrolled_dao = new CourseEnrolledDAO();
$prerequisite_dao = new PrerequisiteDAO();
$result_dao = new ResultDAO();
$round_status_dao = new RoundStatusDAO();
$section_dao = new SectionDAO();
$student_dao = new StudentDAO();


//$new_result = [];


$courseDisplay=[];
foreach($course_dao->retrieveAll() as $one_course){
    $courseDisplay[] = array(
        "course" => $one_course->getCourse(),
        "school"=>$one_course->getSchool(),
        "title"=>$one_course->getTitle(),
        "description"=>$one_course->getDescription(),
        "exam date"=> $one_course->getExamDateJSON(),
        "exam start"=>$one_course->getExamStartJSON(),
        "exam end"=>$one_course->getExamEndJSON()
    );
}
 
$sectionDisplay=[];
foreach($section_dao->retrieveAll() as $one_section){
    $sectionDisplay[]=[
        "course" => $one_section->getCourse(),
        "section"=>$one_section->getSection(),
        "day"=>$one_section->getDay(),
        "start"=>$one_section->getStartJSON(),
        "end"=>$one_section->getEndJSON(),
        "instructor"=>$one_section->getInstructor(),
        "venue"=>$one_section->getVenue(),
        "size"=>$one_section->getSize()
    ];
}

    
$studentDisplay=[];
foreach($student_dao->retrieveAll() as $one_student){
    $studentDisplay[]=[
        "userid"=>$one_student->getUserid(),
        "password"=>$one_student->getPassword(),
        "name"=>$one_student->getName(),
        "school"=>$one_student->getSchool(),
        "edollar"=>$one_student->getEdollarJSON()
    ];
}


// if ( $student != null ) { 
    $result = ["status" => "success", 
                "course"=> $courseDisplay,
                "section"=> $sectionDisplay,
                "student"=>$studentDisplay,
                "prerequisite" => $prerequisite_dao->retrieveAll(),
                "Bid"=>$bid_dao->retrieveAll(),
                "completed-course" => $course_completed_dao->retrieveAll()
                //"section-student"=>
            ];
// } 





// foreach ($result['course'] as $key => $value) {

//     $new_result[] = $value[3];
// }

// $example = ($courses[0]);
// $var1 = $example->exam_start;
// $time = "";
// $time=date("h:i",$example->exam_start);
// if ($var1[0] == "0") {
//     $time = substr($var1,1,4);
// }
// else{
//     $time = substr($var1,0,5);
// }
// var_dump($time);

// $sections=$section_dao->retrieveall();
// for ($i=0; $i <count($sections) ; $i++) { 
//     $section=$sections[i];
//     $start=$course->start[0:5];
//     if($start[0]==0){
//         $start=$start[1:];
//     }
//     $start=explode(":",$course->start);
//     $end=$course->end[0:5];
//     if($end[0]==0){
//         $end=$end[1:];
//     }
//     $end=explode(":",$end);
// }
// $students=$student_dao->retrieveall();
// for ($i=0; $i <count($students) ; $i++) { 
//     $student=$students[$i];
//     $edollar=number_format($student->edollar,1);
// }

// else {
//     $result = ["status" => "error"];
// }

  header('Content-Type: application/json');
  echo json_encode($result, JSON_PRETTY_PRINT);
 
?>