<?php
require_once 'include/common.php';

// implement protect.php later

$userid = $_SESSION['userid'];

// bootstrap tut from https://www.w3schools.com/bootstrap/bootstrap_navbar.asp 
?>
<html>
<head>
  <title>BIOS Admin Home</title>
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

      <li><a href="bootstrap.php">Start Bootstrap</a></li>
      <li><a href='logout.php'>Log Out</a></li>
    </ul>
  </div>
</nav>
  
<div class="container">
  <h3>Hello, <?= $userid ?> and welcome back!</h3><br>

  <form id='stop_r1' action="processClearing.php" method="post">
	Stop Round 1 Bidding: 
	<input type="submit" name="submit" value="Stop">

  <a href='DisplayR1.php' target='_blank' >Click to see round 1 results</a>

  <?php

  ?>

</form>

</div>

</body>
</html>








