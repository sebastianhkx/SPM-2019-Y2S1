<?php
require_once 'include/common.php';

$userid = $_SESSION['userid'];

$student_dao = new StudentDAO();
$bid_dao = new BidDAO();
$r2bid_dao = new R2BidDAO();
// $course_enrolled_dao = new CourseEnrolledDAO();
$isOK = FALSE;
if (isset($_POST['submitbid'])) {
  $course = $_POST['course']; // for repopulating form fields also
  $section = $_POST['section'];
  $amount = $_POST['bidamount'];

  $bidded = new Bid($userid, $amount, $course, $section);
  $errors = $bid_dao->add($bidded);

  if(empty($errors)){
    //$info = $bid_dao->getr2bidinfo($bidded);
    $bids_info = $r2bid_dao->updateBidinfo($bidded);
    $isOK = TRUE;
    $info = $r2bid_dao->getr2bidinfo($bidded);
  }

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
  $course = '';
  $section = '';
  $amount = '';

  $student = $student_dao->retrieve($userid); // student object
  $bids = $bid_dao->retrieveByUser($userid); // could be an array of bids 
  echo "Current Round: 2 (Round start)";
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


  if($isOK){
    $totalbids = sizeof($bids_info);
    echo "<h2>Information:</h2>
        <p>Course:{$info['course']}</p>
        <p>Section:{$info['section']}</p>
        <p>Total Availdable Seats:{$info['vacancy']}</p>
        <p>Total Number Of Bids:$totalbids</p>
        <p>Minimun Bid Value:{$info['min_amount']}</p>";

    echo "<table border='1'>
        <tr>
            <th>No.</th>
            <th>Bid Price</th>
            <th>State</th>
        </tr>";  
    for($i=1;$i <= $totalbids;$i++){
      $bid = $bids_info[$i-1];
      echo "<tr>
            <th>$i</th>
            <th>{$bid[0]}</th>
            <th>{$bid[1]}</th>
          </tr>";
    }
      echo "</table><hr>";
  }      

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