<?php
require_once 'include/common.php';
require_once 'include/protect.php';

$userid = $_SESSION['userid'];

$student_dao = new StudentDAO();
$bid_dao = new BidDAO();
$course = '';
$section = '';
$errors = '';
// var_dump($_SESSION);
if (isset($_SESSION['course'])){
  $course = $_SESSION['course'];
  unset($_SESSION['course']);
}
if (isset($_SESSION['section'])){
  $section = $_SESSION['section'];
  unset($_SESSION['section']);
}
if (isset($_SESSION['errors'])){
  $errors = $_SESSION['errors'];
  unset($_SESSION['errors']);
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
    echo "
    <tr>
        <td>$i</td>
        <td>{$bid->userid}</td>
        <td>{$edollar}</td>
        <td>{$bid->course}</td>
        <td>{$bid->section}</td>
        <td>{$status}</td>
    </tr>";
}

  echo "</table><hr>";

?>

<html>
<body>

  <h2>I want to search :</h2>
  <form action="r2addbid.php" method="POST">
    <table>
      
  <tr>
    <td style='text-align:left'>Course: </td>
    <td><input type="text" name="course" value="<?= $course ?>" required> </td>
    </tr>
  <tr>
    <td style='text-align:left'>Section: </td>
    <td><input type="text" name="section" value="<?= $section ?>" required> </td>
  </tr>

  <tr><td><input type="submit" name='searchsection' value="Search Section" ></td></tr>
</table>

  <font color='red'><?= $errors?></font><br>

  <br>
    <a href='displayCourses.php' target='_blank' >Click to see all courses</a><br>
    <a href='displaySections.php' target='_blank' >Click to see all sections</a>

  <br>   
</div>
</body>
</html>