<?php
require_once 'include/common.php';
require_once 'include/protect.php';

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

    $bidDAO = new BidDAO();
    $resultDAO = new ResultDAO();
    $r2BidDAO = new R2BidDAO();
    $resultObjs = $resultDAO->retrieveByUser($userid);
    $bidObjs =  $bidDAO->retrieveByUser($userid);
    if (!empty($resultObjs) || !empty($bidObjs)){
      echo 
      "
      <table border='1'>
        <tr>
          <th colspan='4'>Bid Results</th>
        </tr>
        <tr>
          <th>Course</th>
          <th>Section</th>
          <th>Amount</th>
          <th>Status</th>
        </tr>";
      foreach ($bidObjs as $bidObj){
        if ($round_status->round_num =='1'){
          $result = 'Pending';
        }
        else{
          $r2BidInfo = $r2BidDAO->getr2bidinfo($bidObj);
          $vacancy = $r2BidInfo->vacancy;
          $clearingPrice = $bidDAO->getRoundTwoSuccessfullPrice($bidObj, $vacancy);
          if ($bidObj->amount>$clearingPrice){
            $result = 'Success';
          }
          else{
            $result = 'Fail';
          }

        }

        $edollar = number_format($bidObj->amount,2);

        echo
        "
        <tr>
          <td>{$bidObj->course}</td>
          <td>{$bidObj->section}</td>
          <td>{$edollar}</td>
          <td>Pending</td>
        </tr>";
      }
      foreach ($resultObjs as $resultObj){
        $result = ucfirst($resultObj->result);
        echo
        "
        <tr>
          <td>{$resultObj->course}</td>
          <td>{$resultObj->section}</td>
          <td>{$resultObj->amount}</td>
          <td>$result</td>
        </tr>";
      }

      echo "</table>";
    }
?>
</div>

</body>
</html>








