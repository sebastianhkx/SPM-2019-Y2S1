<?php
require_once 'include/common.php';
require_once 'include/protect.php';
require_once 'include/clearingLogic.php';

$userid = $_SESSION['userid'];

$student_dao = new StudentDAO();
$bid_dao = new BidDAO();
$courseDAO = new CourseDAO();
$sectionDAO = new SectionDAO();
$r2BidDAO = new R2BidDAO();

if (isset($_POST['course'])){
  $course = $_POST['course'];
  $_SESSION['course'] = $course;
}
if (isset($_POST['section'])){
  $section = $_POST['section'];
  $_SESSION['section'] = $section;
}
if ($courseDAO->retrieveByCourseId($course)==null){
    $_SESSION['errors'] = 'invalid course';
    header("location:r2bidding.php");
    exit();
}
if ($sectionDAO->retrieveSection($course, $section)==null){
    $_SESSION['errors'] = 'invalid section';
    header("location:r2bidding.php");
    exit();
}

if (isset($_POST['bid']) && isset($_POST['bidamount'])){
    $amount = $_POST['bidamount'];
    $bid = new Bid($userid, $amount, $course, $section);
    $errors = $bid_dao->add($bid);
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
      <li><a href='dropbid.php'>Drop Bid</a></li>
      <li><a href='dropsection.php'>Drop Section</a></li>
      <li><a href='logout.php'>Log Out</a></li>
    </ul>
  </div>
</nav>

<div class="container">
<?php
  $amount = '';
  $student = $student_dao->retrieve($userid); // student object
  $bids = $bid_dao->retrieveByUser($userid); // could be an array of bids 

  $edollar = number_format($student->edollar,2);

  echo "Current Round: 2";
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
          <td>$edollar</td>
      </tr>
      </table><hr>";


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

  for ($i=1; $i<=sizeof($bids); $i++) {
      $bid = $bids[$i-1];
      $status = $bid_dao->bidStatus($bid);
      $edollar = number_format($bid->amount,2);
      $r2BidDAO = new R2BidDAO();
      $r2BidInfo = $r2BidDAO->getr2bidinfo($bid);
      $vacancy = $r2BidInfo->vacancy;
      $clearingPrice = $bid_dao->getRoundTwoSuccessfullPrice($bid, $vacancy);
      if ($bid->amount>$clearingPrice){
        $result = 'Success';
      }
      else{
        $result = 'Fail';
      }
      echo "
      <tr>
          <td>$i</td>
          <td>{$bid->userid}</td>
          <td>{$edollar}</td>
          <td>{$bid->course}</td>
          <td>{$bid->section}</td>
          <td>$result</td>
      </tr>";
  }

  echo "</table><hr>";

?>

<html>

<?php

$temp_bid = new Bid($userid, 10, $course, $section);
$currentbids = $bid_dao->retrieveByCourseSection([$course, $section]);
$totalbids = sizeof($currentbids);
$r2Info = $r2BidDAO->getr2bidinfo($temp_bid);

echo "<h2>Information:</h2>
        <p>Course:{$course}</p>
        <p>Section:{$section}</p>
        <p>Total Available Seats:{$r2Info->vacancy}</p>
        <p>Total Number Of Bids:$totalbids</p>
        <p>Minimum Bid Value:{$r2Info->min_amount}</p>";
    // if($totalbids > 0){
    //   echo "<table border='1'>
    //       <tr>
    //           <th>No.</th>
    //           <th>Bid Price</th>
    //           <th>State</th>
    //       </tr>";  
    //   for($i=1;$i <= $totalbids;$i++){
    //     $bid = $currentbids[$i-1];
    //     $status = $bid_dao->bidStatus($bid);
    //     echo "<tr>
    //           <th>$i</th>
    //           <th>{$bid->amount}</th>
    //           <th>{$status}</th>
    //         </tr>";
    //   }
    //     echo "</table><hr>";
    // }
?>
<body>

  <h2>Bid Amount :</h2>
  <form action="r2addbid.php" method="POST">
    <table>
      
  <tr>
    <td style='text-align:left'>Course: </td>
    <td><input type="text" name="course" value="<?= $course ?>" readonly="readonly"> </td>
    </tr>
  <tr>
    <td style='text-align:left'>Section: </td>
    <td><input type="text" name="section" value="<?= $section ?>" readonly="readonly"> </td>
  </tr>

  <tr>
    <td style='text-align:left'> Bid Amount: </td>
    <td><input type="number" name="bidamount" placeholder="min 10.00" step="0.01" min="10.00" value="<?= $amount ?>" required>  </td>
  </tr>


  <tr><td><input type="submit" name='bid' value="Bid" ></td></tr>
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