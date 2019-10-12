<?php
require_once 'include/common.php';

$userid = $_SESSION['userid'];

$student_dao = new StudentDAO();
$bid_dao = new BidDAO();
$course_enrolled_dao = new CourseEnrolledDAO();

if (isset($_POST['submitbid'])) {
  $course = $_POST['course']; // for repopulating form fields also
  $section = $_POST['section'];
  $amount = $_POST['bidamount'];

  $bidded = new Bid($userid, $amount, $course, $section);
  $errors = $bid_dao->add($bidded);
}
?>
<html>
    <style>
        th, td {
            text-align: center;
        }
    </style>
</html>
<html>
<head>
  <title>BIOS Bidding</title>
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
      <li class="active"><a href="r2bidding.php">Bidding</a></li>
      <li><a href='r2dropbid.php'>Drop Bid</a></li>
      <li><a href='logout.php'>Log Out</a></li>
    </ul>
  </div>
</nav>

<div class="container">
<?php

//   // Displays the current active round
//   $roundstatus_dao = new RoundStatusDAO();
//   $round_status = $roundstatus_dao->retrieveCurrentActiveRound();
//   if ($round_status != null) {
//     $round_num = $round_status->round_num;
//     if($round_num == 2){
//       header("location:r2bidding.php");
//       exit();
//     }
//     //echo "<h1>Current Round: $round_status->round_num</h1>";
//   }
//   else {
//     echo "<h1>No active bidding round currently.</h1>";
//   }
//   echo "<hr>";

  $course = '';
  $section = '';
  $amount = '';

  $student = $student_dao->retrieve($userid); // student object
  $bids = $bid_dao->retrieveByUser($userid); // could be an array of bids 
  $courses_enrolled = $course_enrolled_dao->retrieveByUserid($userid);//courses enrolled object

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

  echo "<h2>Your current courses enrolled:</h2>";

  echo "<table border='1'>
      <tr>
          <th>No.</th>
          <th>Amount</th>
          <th>Course</th>
          <th>Section</th>
          <th>Status</th>
      </tr>";

  for ($i = 1; $i <= count($courses_enrolled); $i++) {
      $course_enrolled = $courses_enrolled[$i-1];
      echo "
      <tr>
          <td>$i</td>
          <td>$course_enrolled->amount</td>
          <td>$course_enrolled->course</td>
          <td>$course_enrolled->section</td>
          <td>$course_enrolled->result</td>
      </tr>";
  }

  echo "</table><hr>";

  echo "<h2>Your current bids:</h2>";

  echo "<table border='1'>
      <tr>
          <th>No.</th>
          <th>User ID</th>
          <th>Amount</th>
          <th>Course</th>
          <th>Section</th>
          <th>Status</th>
      </tr>";

  for ($i = 1; $i <= count($bids); $i++) {
      $bid = $bids[$i-1];
      echo "
      <tr>
          <td>$i</td>
          <td>$bid->userid</td>
          <td>$bid->amount</td>
          <td>$bid->course</td>
          <td>$bid->section</td>
          <td>Placeholder</td>
      </tr>";
  }

  echo "</table><hr>";

?>

<html>
<body>

  <h2>I want to search :</h2>
  <form action="r2bidding.php" method="POST">
    <table>
      
  <tr>
    <td style='text-align:left'>Course: </td>
    <td><input type="text" name="course" value="<?= $course ?>" required> </td>
    </tr>
  <tr>
    <td style='text-align:left'>Section: </td>
    <td><input type="text" name="section" value="<?= $section ?>" required> </td>
  </tr>
  <tr>
    <td style='text-align:left'>Bid Amount: </td>
    <td><input type="number" name="bidamount" placeholder="min 10.00" step="0.01" min="10.00" value="<?= $amount ?>" required>  </td>
  </tr>

  <tr><td><input type="submit" name='submitbid' value="Confirm Bid" ></td></tr>
</table>
  <br>
  <?php
  // if user submits a new bid
  if (isset($errors)) {
    if (is_array($errors)){
      echo "<font color='red'>Error!</font><br><ul>";
      foreach($errors as $err) {
        echo "<font color='red'><li>$err</li></font>";
      }
      echo "</ul>";
    }
    else {
      echo "<font color='green'>Bid was added successfully!<br>
            e$ updated</font><br>";
    
    }
  }
  
  ?>
  <br>
    <a href='displayCourses.php' target='_blank' >Click to see all courses</a><br>
    <a href='displaySections.php' target='_blank' >Click to see all sections</a>


  <br>   
</div>
</body>
</html>