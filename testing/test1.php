<?php
require_once '../app/include/common.php';
$dao = new CourseDAO;
$pdo = new SectionDAO;
$pdo2 = new course_completedDAO;



// // test case 1
// $school = "SIS";
// $msg = $dao->retrieveBySchool($school);
// var_dump($msg);

// // test case 2
// $section = "S1";
// $msg2 = $pdo->retrieveBySection($section);
// var_dump($msg2);

// // test case 3
// $userid = "ben.ng.2009";
// $msg3 = $pdo2->retrieve($userid);
// var_dump($msg3);

?>