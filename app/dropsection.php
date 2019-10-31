<?php
require_once 'include/common.php';
require_once 'include/protect.php';

$userid = $_SESSION['userid'];
$courseEnrolledDAO = new CourseEnrolledDAO;
$r2bidDAO = new R2BidDAO();

$page = "bidding.php";
$message = "";

//check round number
$roundstatus_dao = new RoundStatusDAO();
$round_status = $roundstatus_dao->retrieveCurrentActiveRound();
// var_dump($round_status);
if($round_status != null){

  if($round_status->round_num == 1){
    $page = "bidding.php";
    // $message =  "no course enrolled!";
  }
  else{
    $page = "r2bidding.php";
  }
  $round_active = TRUE;

}
else{
  $message =  "round not active";
  $round_active = FALSE;
}

if (isset($_POST["dropped_section"])){
    //var_dump($_POST["dropped_section"]);
    $drop_courses = $_POST['dropped_section'];
    $errors = [];
    foreach($drop_courses as $dropcourse){
      $section = $courseEnrolledDAO->retrieveByUseridCourse($userid, $dropcourse);
      $errors[] = $r2bidDAO->r2dropSection($userid,$dropcourse,$section->section);
    }
}

// bootstrap tut from https://www.w3schools.com/bootstrap/bootstrap_navbar.asp 
?>
<html>
<head>
  <title>BIOS Home</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
</head>
<body>

<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand">BIOS</a>
    </div>
    <ul class="nav navbar-nav">
      <li><a href="home.php">Home</a></li>
      <li><a href=<?=$page?>>Bidding</a></li>
      <li><a href='dropbid.php'>Drop Bid</a></li>
      <li class="active"><a href="#">Drop Section</a></li>
      <li><a href='logout.php'>Log Out</a></li>
    </ul>
  </div>
</nav>
  
<div class="container">

<?php
//gets lists of course enrolled
$course_enrolled = $courseEnrolledDAO->retrieveByUserid($_SESSION['userid']);
//var_dump($course_enrolled);
//var_dump($_SESSION);
$resultDAO = new ResultDAO;

// $message =  "no course enrolled!";
if (!empty($course_enrolled) && $round_active){
  $message = "";
?>
<form method='post' action='dropsection.php'>
    <table border='2'>
        <tr>
            <th>Course</th>
            <th>Section</th>
            <th>Day</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Exam Date</th>
            <th>Exam Start Time</th>
            <th>Exam End Time</th>
            <th>Amount</th>
            <th>Drop</th>
        </tr>
<?php
// var_dump($course_enrolled);
foreach ($course_enrolled as $course){
    $bid_result = $resultDAO->retrieveByCourseEnrolled($course);
    // var_dump($bid_result);
    $amount = $bid_result->amount;
    echo 
"
        <tr>
            <td>{$course->course}</td>
            <td>{$course->section}</td>
            <td>$course->day</td>
            <td>$course->start</td>
            <td>$course->end</td>
            <td>$course->exam_date</td>
            <td>$course->exam_start</td>
            <td>$course->exam_end</td>
            <td>$amount</td>
            <td> <input type='radio' name='dropped_section[]' value={$course->course}> </td>
        </tr>
";
}

?>
    </table>
        <br><input type='submit' value='Drop Section(s)'>
</form>
<?php


}//closes if (!empty($course_enrolled)){
  $student_dao = new StudentDAO();
  $student = $student_dao->retrieve($userid); // student object
  
  echo "<h2>Your info:</h2>";
  echo "<table border=1>
      <tr>
          <th>Name</th>
          <td>$student->name</td>
      </tr>  
      <tr>
          <th>School</th>
          <td>$student->school</td>
      </tr>
      <tr>
          <th>e$ Balance</th>
          <td>$student->edollar</td>
      </tr>
      </table><hr>";

if (isset($errors) && $round_active){
  // var_dump($message);
  if (is_array($errors)){
    echo "<font color='green'>Bid was dropped successfully!<br>
            e$ updated</font><br>";
  }
  else{
    echo "<font color='red'>Error!</font><br><ul>";
      foreach($errors as $err) {
        echo "<font color='red'><li>$err</li></font>";
      }
    echo "</ul>";
  }
}
else{
  echo "<font color='red'>$message</font><br><ul>";
}
?>
</div>

</body>
</html>
