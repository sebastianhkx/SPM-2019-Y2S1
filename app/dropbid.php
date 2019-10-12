<?php
    require_once 'include/common.php';
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
  <title>BIOS Drop Bid</title>
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
      <li class="active"><a href='dropbid.php'>Drop Bid</a></li>
      <li><a href='logout.php'>Log Out</a></li>
    </ul>
  </div>
</nav>
<div class="container">

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

    $coursedrop = '';
    $sectiondrop = '';

    $userid = $_SESSION['userid'];

    $student_dao = new StudentDAO();
    $bid_dao = new BidDAO();

    $errors = '';
    if (isset($_POST['submitdrop'])){
        $coursedrop = $_POST['coursedrop']; // for repopulating form fields also
        $sectiondrop = $_POST['sectiondrop'];

        $bid_to_drop_temp = new Bid($userid, 0, $coursedrop, $sectiondrop); // the current drop bid method doesn't need amount. might need to revisit the method.
        $bid_to_drop = new Bid($userid, $bid_dao->checkExistingBid($bid_to_drop_temp), $coursedrop, $sectiondrop);
        $errors = $bid_dao->drop($bid_to_drop);
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
?>

<html>
<body>
    <h2>I want to drop this bid:</h2>
    <form action="dropbid.php" method="POST">
        <table>
        <tr><td style='text-align:left'>
    Course: </td><td><input type="text" name="coursedrop" value="<?= $coursedrop ?>" required> </td></tr>
    <tr><td style='text-align:left'>
    Section: </td><td><input type="text" name="sectiondrop" value="<?= $sectiondrop ?>" required> </td></tr>
    <tr><td>
    <input type="submit" name='submitdrop' value="Drop Bid" ></td></tr>
    </table>
    <br>

<?php
    // if user drops a bid
   
    if (is_array($errors)) {
    echo "<font color='red'>Error!</font><br>
    <ul>";
    foreach($errors as $err) {
        echo "<font color='red'><li>$err</li></font>";
    }
    echo "</ul>";
    }
    elseif ($errors!='') {
    echo "<font color='green'>Bid was dropped successfully!<br>
            e$ updated</font>";
    }   
?>
</div>
</body>
</html>
