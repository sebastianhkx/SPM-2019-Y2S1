<?php
require_once 'include/common.php';
require_once 'include/protect.php';
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

      <li><a href="bidding.php">Bidding</a></li>
      <li><a href='dropbid.php'>Drop Bid</a></li>
      <li><a href='logout.php'>Log Out</a></li>
    </ul>
  </div>
</nav>

<?php
$dao = new BidDAO();
$bids = $dao->retrieveAll();

echo "<table border='1'>
    <tr>
        <th>No.</th>
        <th>User ID</th>
        <th>Amount</th>
        <th>Course</th>
        <th>Section</th>
    </tr>";

for ($i = 1; $i <= count($bids); $i++) {
    $bid = $bids[$i-1];
    $edollar = number_format($bid->amount,2);
    echo "
    <tr>
        <td>$i</td>
        <td>$bid->userid</td>
        <td>$edollar</td>
        <td>$bid->code</td>
        <td>$bid->section</td>
    </tr>";
}

?>
