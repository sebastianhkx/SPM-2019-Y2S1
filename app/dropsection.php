<?php
require_once 'include/common.php';

// implement protect.php later

$userid = $_SESSION['userid'];

if (isset($_POST["dropped_section"])){
    var_dump($_POST["dropped_section"]);
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

      <li><a href="bidding.php">Bidding</a></li>
      <li><a href='dropbid.php'>Drop Bid</a></li>
      <li class="active"><a href="#">Drop Section</a></li>
      <li><a href='logout.php'>Log Out</a></li>
    </ul>
  </div>
</nav>
  
<div class="container">

<?php
//gets lists of course enrolled
$courseEnrolledDAO = new CourseEnrolledDAO;
$course_enrolled = $courseEnrolledDAO->retrieveByUserid($_SESSION['userid']);
var_dump($course_enrolled);
var_dump($_SESSION);
$resultDAO = new ResultDAO;


if (!empty($course_enrolled)){
?>
<form method='post' action='dropsection.php'>
    <table border='1'>
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
            <th>Tick to drop</th>
        </tr>
<?php
foreach ($course_enrolled as $course){
    $bid_result = $resultDAO->retrieveByCourseEnrolled($course);
    var_dump($bid_result);
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
            <td> <input type='checkbox' name='dropped_section[]' value=$course->course> </td>
        </tr>
";
}

?>

        <tr>
            <td align='right' colspan='10'><input type='submit' value='Drop Bids'></td>
        </tr>
    </table>
</form>
<?php
}//closes if (!empty($course_enrolled)){
?>
</div>

</body>
</html>
