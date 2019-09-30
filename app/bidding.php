<?php
require_once 'include/common.php';
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
      
      <li class="active"><a href="#">Bidding</a></li>
      <li><a href='dropbid.php'>Drop Bid</a></li>
      <li><a href='logout.php'>Log Out</a></li>
    </ul>
  </div>
</nav>

<?php

  // Displays the current active round
  $roundstatus_dao = new RoundStatusDAO();
  $round_status = $roundstatus_dao->retrieveCurrentActiveRound();
  if ($round_status != null) {
    echo "<h1>Current Round: $round_status->round_num</h1>";
  }
  else {
    echo "<h1>No active bidding round currently.</h1>";
  }
  echo "<hr>";

  $userid = $_SESSION['userid'];

  $student_dao = new StudentDAO();
  $bid_dao = new BidDAO();

  $display = "";
  // if user wants to add a bid
  if (isset($_POST['see_add_bid'])) {
    $course = '';
    $section = '';
    $amount = '';
    $display = '<h2>I want to bid for:</h2>
    <form action="bidding.php" method="POST">
    Course: <input type="text" name="course" required> <br>
    Section: <input type="text" name="section" required> <br>
    Bid Amount: <input type="number" name="bidamount" placeholder="1.00" step="0.01" min="10.00" required> <br>
    <input type="submit" name="submitbid" value="Confirm Bid" >';
  }

  // if user wants to drop a bid
  if (isset($_POST['see_drop_bid'])) {
    $coursedrop = '';
    $sectiondrop = '';
    $display = '<h2>I want to drop this bid:</h2>
    <form action="bidding.php" method="POST">
    Course: <input type="text" name="coursedrop" required> <br>
    Section: <input type="text" name="sectiondrop" required> <br>
    <input type="submit" name="submitdrop" value="Drop Bid" >';
  }

  $student = $student_dao->retrieve($userid); // student object
  $bids = $bid_dao->retrieveByUser($userid); // could be an array of bids

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

  // if user submits a new bid
  if (isset($_POST['submitbid'])) {
    $course = $_POST['course']; // for repopulating form fields also
    $section = $_POST['section'];
    $amount = $_POST['bidamount'];

    $bidded = new Bid($userid, $amount, $course, $section);
    $errors = $bid_dao->add($bidded);
    if (is_array($errors)) {
      echo "Errors:<br><ul>";
      foreach($errors as $err) {
        echo "<li>$err</li>";
      }
      echo "</ul>";
    }
    else {
      echo "<h1>Bid was added successfully!</h1><br>
            <h1>e$ updated</h1>";
    }
  }

  // if user drops a bid
  if (isset($_POST['submitdrop'])){
    $coursedrop = $_POST['coursedrop']; // for repopulating form fields also
    $sectiondrop = $_POST['sectiondrop'];

    $bid_to_drop_temp = new Bid($userid, 0, $coursedrop, $sectiondrop); // the current drop bid method doesn't need amount. might need to revisit the method.
    $bid_to_drop = new Bid($userid, $bid_dao->checkExistingBid($bid_to_drop_temp), $coursedrop, $sectiondrop);
    $errors = $bid_dao->drop($bid_to_drop);
    if (is_array($errors)) {
      echo "Errors:<br><ul>";
      foreach($errors as $err) {
        echo "<li>$err</li>";
      }
      echo "</ul>";
    }
    else {
      echo "<h1>Bid was dropped successfully!</h1><br>
            <h1>e$ updated</h1>";
    }
  }

?>

<html>
<body>

  <h3>Choose an option:</h3>
  <form action="bidding.php" method="POST">
    <input type="submit" value="I want to add a bid" name="see_add_bid">
    <input type="submit" value="I want to drop a bid" name="see_drop_bid">
  </form>

  <?php
    echo $display;
  ?>

  <!-- <h2>I want to bid for:</h2>
  <form action="bidding.php" method="POST">
  Course: <input type="text" name="course" value="<?= $course ?>" required> <br>
  Section: <input type="text" name="section" value="<?= $section ?>" required> <br>
  Bid Amount: <input type="number" name="bidamount" placeholder="1.00" step="0.01" min="10.00" value="<?= $amount ?>" required> <br>
  <input type="submit" name='submitbid' value="Confirm Bid" > -->

  <!-- <h2>I want to drop this bid :</h2>
  <form action="bidding.php" method="POST">
  Course: <input type="text" name="coursedrop" value="<?= $coursedrop ?>" required> <br>
  Section: <input type="text" name="sectiondrop" value="<?= $sectiondrop ?>" required> <br>
  <input type="submit" name='submitdrop' value="Drop Bid" > -->

  <br>
  <br>

    <a href='displaycourses.php' target='_blank' >Click to see all courses</a>

  <br>   
</body>
</html>