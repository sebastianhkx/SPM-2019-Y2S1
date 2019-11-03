<?php

require_once 'include/common.php';
require_once 'include/protect.php';
$userid = $_SESSION['userid'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>BIOS Round 1 Result</title>

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">

<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top" id="mainNav">
    <div class="container">
      <a class="navbar-brand" href="home_admin.php">Merlion University BIOS</a>
      
      <ul class="navbar-nav ml-left">
          <li class="nav-item">
            <a class="nav-link">&nbsp;</a>
          </li>
      </ul>

      <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="home_admin.php">Home&nbsp;</a>
          </li>
      </ul>
      <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="bootstrap.php">Start Bootstrap&nbsp;</a>
          </li>
      </ul>

        <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link"><?= $userid ?>&nbsp;</a>
        </li>
        </ul>
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="logout.php">Logout</a>
          </li>
        </ul>

      </div>
    </div>
  </nav>

<!-- Buffer space -->
<div class="container">
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <br>
    <br>
    <br>
  </div>
<div class="container-fluid">
    <h1 class="h3 mb-0 text-gray-800">Round 2 Bidding Result</h1>

<?php


$resultDAO = new ResultDAO();
$results = $resultDAO->retrieveByRound(1);

echo "<table border='1'>
    <tr>
        <th>userid</th>
        <th>amount</th>
        <th>course</th>
        <th>section</th>
        <th>result</th>
        <th>round_num</th>
    </tr>";

for ($i = 1; $i <= count($results); $i++) {
    $result = $results[$i-1];
    $edollar = number_format($result->amount,2);
    echo "
    <tr>
        <td>$result->userid</td>
        <td>$edollar</td>
        <td>$result->course</td>
        <td>$result->section</td>
        <td>$result->result</td>
        <td>$result->round_num</td>
    </tr>";
}

echo "</table>";

// var_dump($results);
?>

<!-- end of page -->
</div>

<!-- Bootstrap core JavaScript-->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="js/sb-admin-2.min.js"></script>

</body>
</html>
