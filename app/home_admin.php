<?php
require_once 'include/common.php';

// implement protect.php later

$userid = $_SESSION['userid'];
$r1_disabled = 'disabled';
$r2_disabled = 'disabled';

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
  <h3>Hello <?= $userid ?> and welcome back!</h3><br>
<?php
$roundstatus_dao = new RoundStatusDAO();
if (!empty($round_status)){
  $round_status = $roundstatus_dao->retrieveCurrentActiveRound();
// echo $round_status->round_num;
// echo $round_status->status;
?>
<!-- Round 1 controls-->
  <form id='stop_r1' action="processclearing.php" method="post">
	Round 1 Bidding: 
  <?php
  if ($round_status->round_num ==  '1') {
    $r1_disabled = '';
  }
  echo "
  <input type='submit' name='submit' value='Stop' $r1_disabled >";

  if ($round_status->round_num ==  '1' && $round_status->status == 'ended') {
    echo "
    <a href='displayr1.php' target='_blank'> Click to see round 1 results </a><br><br>
    ";
  }
  ?>
  </form>


<!-- Round 2 controls-->
  <form id='stop_r2' action="processclearing.php" method="post">
	Round 2 Bidding: 
  <?php
  if ($round_status->round_num ==  '2') {
    $r2_disabled = '';
  }

  echo "
  <input type='submit' name='submit' value='Stop' $r2_disabled >";
  
  if ($round_status->round_num ==  '2' && $round_status->status == 'ended') {
    echo "
    <a href='displayr2.php' target='_blank' >Click to see round 2 results</a>
    ";
  }
}//if (!empty($round_status)){  ends here

  ?>
  </form>

</div>

</body>
</html>








