<?php
require_once 'include/common.php';
require_once 'include/clearingLogic.php';

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
      <li><a href="home_admin.php">Home</a></li>

      <li><a href="bootstrap.php">Start Bootstrap</a></li>
      <li><a href='logout.php'>Log Out</a></li>
    </ul>
  </div>
</nav>

<div class="container">
<?php

    $roundstatus_dao = new RoundStatusDAO();

    if (isset($_POST['stop_r1']) || isset($_POST['stop_r2'])) {
      $roundstatus_dao->stopRound();
      roundOneClearing();
      header("Location: home_admin.php");
      exit;
    }
    
    elseif (isset($_POST['start_r1']) || isset($_POST['start_r2'])) {
      $roundstatus_dao->startRound();
      header("Location: home_admin.php");
      exit;
    }

    // roundOneClearing();
    
?>
</table>
</div> <!--div container ends here-->
</body>
</html>
