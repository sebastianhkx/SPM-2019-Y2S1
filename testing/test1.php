<?php
require_once '../app/include/common.php';
$dao = new CourseDAO;
$pdo = new SectionDAO;
$pdo2 = new course_completedDAO;
$pdo3 = new BidDAO;



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

// $bid = new Bid("ben.ng.2009", 12, "IS100", "S1");
// $result = $pdo3->add($bid);


?>