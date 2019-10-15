<?php
require_once 'include/common.php';

// implement protect.php later

$userid = $_SESSION['userid'];

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
      <li class="active"><a href="#">Home</a></li>

      <li><a href="bidding.php">Bidding</a></li>
      <li><a href='dropbid.php'>Drop Bid</a></li>
      <li><a href='dropsection.php'>Drop Section</a></li>
      <li><a href='logout.php'>Log Out</a></li>
    </ul>
  </div>
</nav>
  
<div class="container">
  <h3>Hello, <?= $userid ?> and welcome back!</h3>

<?php
    $roundstatus_dao = new RoundStatusDAO();
    $round_status = $roundstatus_dao->retrieveCurrentActiveRound();
    if ($round_status != null) {
        echo "<h1>Current Round: $round_status->round_num</h1>";
    }
    else {
        echo "<h1>No active bidding round currently.</h1>";
    }
    echo "<hr>";
?>
</div>

</body>
</html>








