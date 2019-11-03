<?php
require_once 'include/common.php';
require_once 'include/protect.php';

$userid = $_SESSION['userid'];
// protect user page from admin
if ($userid === "admin") {
  header("Location: login.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>BIOS Student Home</title>
  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">

<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top" id="mainNav">
    <div class="container">
      <a class="navbar-brand" href="home.php">Merlion University BIOS</a>
      
      <ul class="navbar-nav ml-left">
          <li class="nav-item">
            <a class="nav-link">&nbsp;</a>
          </li>
      </ul>

      <ul class="navbar-nav">
          <li class="nav-item active">
            <a class="nav-link" href="home.php">Home&nbsp;</a>
          </li>
      </ul>
      <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="bidding.php">Bidding&nbsp;</a>
          </li>
      </ul>
      <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="dropbid.php">Drop Bid&nbsp;</a>
          </li>
      </ul>
      <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="dropsection.php">Drop Section&nbsp;</a>
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
<?php
$roundstatus_dao = new RoundStatusDAO();
$round_status = $roundstatus_dao->retrieveCurrentActiveRound();

// message for end of rounds
$round_statuses = $roundstatus_dao->retrieveAll();
$round1_arr = [$round_statuses[0]->round_num, $round_statuses[0]->status];
$round2_arr = [$round_statuses[1]->round_num, $round_statuses[1]->status];
?>

<html>
<!-- Page Content start here-->
<div class="container-fluid">
    <!-- Round status -->
    <div class="col-lg-6 mb-4">
      <div class="card shadow mb-4">
      <div class="card-header ">

        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">
            <?php
              if ($round_status != null) {
                echo "Current Round: $round_status->round_num";
              }
              elseif ($round1_arr == [1, 'ended'] && $round2_arr == [2, 'pending']) {
                echo "Round 1 bidding has ended, please check your results";
              }
              elseif ($round1_arr == [1, 'ended'] && $round2_arr == [2, 'ended']) {
                echo "Round 2 bidding has ended, please check your results";
              }
              else {
                echo "No active bidding round currently";
              }
            ?>
          </h6>
        </div>
      </div>
      </div>

    </div>

            <!-- User info -->
    <div class="col-lg-6 mr-4">
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
              Your Info
            </h6>
          </div>
        <br>
        <!-- <div class='text-center'> -->
        <?php
        $student_dao = new StudentDAO();
        $student = $student_dao->retrieve($userid);
        $edollar = number_format($student->edollar,2);
        ?>

        <table class="table table-bordered">
          <tr>
              <th>Name</th>
              <td><?= $student->name ?></td>
          </tr>  
          <tr>
              <th>School</th>
              <td><?= $student->school ?></td>
          </tr>
          <tr>
              <th>e$ Balance</th>
              <td><?= $edollar ?></td>
          </tr>
        </table>
          </div>
        
      </div>
    </div>

    <div class="col-lg-6 mb-4">
      <div class="card shadow mb-4">
          <div class="card-header py-3">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Bidding Overview</h6>
            </div>
            <br>
            <?php
            $bidDAO = new BidDAO();
            $resultDAO = new ResultDAO();
            $r2BidDAO = new R2BidDAO();
            $resultObjs = $resultDAO->retrieveByUser($userid);
            $bidObjs =  $bidDAO->retrieveByUser($userid);
            if (!empty($resultObjs) || !empty($bidObjs)){
              echo 
              "
              <table class='table table-bordered'>
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
                echo "
                <tr>
                  <td>{$bidObj->course}</td>
                  <td>{$bidObj->section}</td>
                  <td>{$edollar}</td>
                  <td>Pending</td>
                </tr>";
              }
              foreach ($resultObjs as $resultObj){
                $result = ucfirst($resultObj->result);
                $edollar = number_format($resultObj->amount,2);
                echo "
                <tr>
                  <td>{$resultObj->course}</td>
                  <td>{$resultObj->section}</td>
                  <td>{$edollar}</td>
                  <td>$result</td>
                </tr>";
              }
              echo "</table>";
            }
            ?>
          </div>
      </div>
    </div>

<!-- page end -->
</div>

  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin-2.min.js"></script>

  <!-- Page level plugins -->
  <script src="vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="js/demo/datatables-demo.js"></script>

</body>

</html>








