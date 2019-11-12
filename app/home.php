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


<nav class="navbar navbar-expand-md navbar-dark bg-dark" id="mainNav">
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
<!-- <div class="container-fluid">
  <div class='row'> -->

<!-- Page Wrapper -->
<div id="wrapper">

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

  <!-- Main Content -->
  <div id="content">

    <!-- Begin Page Content -->
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-5">

    <!-- Round status -->
    <div class="col-auto mt-4">
      <div class="card shadow ">
        <div class="card-header">

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
    <!-- end of round status -->

    <!-- User info -->
    <div class="col-auto">
      <div class="card shadow mb-4">
        <div class="card-header py-3">

          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
              Your Info
            </h6>
          </div>
          <br>
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
    <!-- end of info -->

    <!-- bid overview -->
    <div class="col-auto">
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
            $r_num = "";
            if (!empty($resultObjs) || !empty($bidObjs)){
              echo 
              "
              <table class='table table-responsive table-bordered'>
                <tr>
                  <th>Round</th>
                  <th>Course</th>
                  <th>Section</th>
                  <th>Amount</th>
                  <th>Status</th>
                </tr>";
              foreach ($bidObjs as $bidObj){
                if ($round_status->round_num =='1'){
                  $result = 'Pending';
                  $r_num = '1';
                }
                else{
                  $r_num = '2';
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
                  <td>$r_num</td>
                  <td>{$bidObj->course}</td>
                  <td>{$bidObj->section}</td>
                  <td>{$edollar}</td>
                  <td>Pending</td>
                </tr>";
              }
              foreach ($resultObjs as $resultObj){
                $result = ucfirst($resultObj->result);
                $edollar = number_format($resultObj->amount,2);
                $r_num = $resultObj->round_num;
                echo "
                <tr>
                  <td>$r_num</td>
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
<!-- end of bid overview -->
</div>

<div class="col-lg-7">

                   <!-- Timetable last row to right col -->
    <div class="col-auto mt-4">
      <div class="card shadow mb-6">
        <div class="card-header mb-6">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
              Your timetable
            </h6>
            <?php
              $bidDAO = new BidDAO();
              $courseEnrolledDAO = new CourseEnrolledDAO();
              $bids = $bidDAO->retrieveByUser($userid);
              $courseEnrolled = $courseEnrolledDAO->retrieveByUserid($userid);
              $date = [1=>'Mon',2=>'Tue',3=>'Wed',4=>'Thu',5=>'Fri',6=>'Sat',7=>'Sun'];
              $time = ['08:00'];
              #generate time from 08:00 to 22:45
              while ($time[sizeof($time)-1] != '22:45'){
                $time[] = date('H:i',strtotime($time[sizeof($time)-1])+strtotime('00:15:00'));
              }
              #calculate rowspan of bids and course enrolled
              $sectionDAO = new SectionDAO();
              $bidtimetable = [];
              $courseenrolledtimetable = [];
              foreach ($bids as $bid){
                $sectionObj = $sectionDAO->retrieveBySection($bid);
                $rowspan = (strtotime($sectionObj->end)-strtotime($sectionObj->start))/900;
                if (isset($bitimetable[$sectionObj->day])){
                  $bidtimetable[$sectionObj->day][substr($sectionObj->start,0,5)] = [$sectionObj, $rowspan];
                }
                else{
                  $bidtimetable[$sectionObj->day] = [substr($sectionObj->start,0,5)=>[$sectionObj, $rowspan]];
                }
              }

              foreach ($courseEnrolled as $courseEnroll){
                $rowspan = (strtotime($courseEnroll->end)-strtotime($courseEnroll->start))/900;
                if (isset($bitimetable[$courseEnroll->day])){
                  $courseenrolledtimetable[$courseEnroll->day][substr($courseEnroll->start,0,5)] = [$courseEnroll, $rowspan];
                }
                else{
                  $courseenrolledtimetable[$courseEnroll->day] = [substr($courseEnroll->start,0,5)=>[$courseEnroll, $rowspan]];
                }
              }
            ?>
          </div>

          <div class="card-header py-3   d-flex flex-row align-items-center justify-content-between">
          <!-- <table class='table table-responsive table-bordered'> -->
          <table border='1' cellpadding='4'>
            <tr>
              <th >Time</th>
            <?php
              foreach ($date as $num=>$day){
                echo "<th width='100'> $day </th>";
              }
            ?>
            </tr>
            <?php
              $skipday = [1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0, 7=>0];
              $rowskip = 0;
              foreach ($time as $colname){
                echo "<tr>";
                if ($rowskip==0){
                  echo "<td rowspan='4'>$colname</td>";
                  
                }
                foreach ($date as $num=>$day){
                  if (isset($bidtimetable[$num][$colname])){
                    echo "<td bgcolor='#B580D1' rowspan='{$bidtimetable[$num][$colname][1]}'><font color='black'>{$bidtimetable[$num][$colname][0]->course}</font></td>";
                    $skipday[$num] = $bidtimetable[$num][$colname][1];
                  }
                  if (isset($courseenrolledtimetable[$num][$colname])){
                    echo "<td bgcolor='green' rowspan='{$courseenrolledtimetable[$num][$colname][1]}'><font color='black'>{$courseenrolledtimetable[$num][$colname][0]->course}</font></td>";
                    $skipday[$num] = $courseenrolledtimetable[$num][$colname][1];
                  }
                  if ($skipday[$num]==0){
                    echo "<td height='2'></td>";
                  }
                  else{
                    $skipday[$num] -= 1;  
                  }
                }
                echo "</tr>";
                $rowskip = ($rowskip+1)%4;
              }
            ?>
          </table>
          <hr>
          
          </div>

        </div>
      </div>
    </div>

<!-- Exam schedule -->
<div class="col-auto mt-4">
      <div class="card shadow mb-6">
        <div class="card-header mb-6">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
              Exam Schedule
            </h6>

          <div class="card-header py-3   d-flex flex-row align-items-center justify-content-between">

          <table class='table table-responsive table-bordered'>
            <tr>
              <td>Course</td>
              <td>Section</td>
              <td>Exam Date</td>
              <td>Start Time</td>
              <td>End Time</td>
              <td>Status</td>
            </tr>
            <?php
            //$bidDAO = new BidDAO();
            $courseDAO = new CourseDAO();
            //$sectionDAO = new SectionDAO();
            //$bids = $bidDAO->retrieveByUser($userid);
            foreach ($bids as $bid){
              $sectionObj = $sectionDAO->retrieveBySection($bid);
              $courseId = $bid->course;
              $section = $bid->section;
              $courseObj = $courseDAO->retrieveByCourseId($courseId);
              $exam_date = $courseObj->exam_date;
              $start_time = $courseObj->exam_start;
              $end_time = $courseObj->exam_end;
              $status = "Pending";
              //$sectionObj = $sectionDAO->retrieveBySection($bid);
              echo "
              <tr>
              <td>$courseId</td>
              <td>$section</td>
              <td>$exam_date </td>
              <td>$start_time</td>
              <td>$end_time </td>
              <td>$status </td>
              </tr>";
            }
            foreach($courseEnrolled as $one_courseEnrolled){
              $courseId = $one_courseEnrolled->course;
              $section = $one_courseEnrolled->section;
              $exam_date = $one_courseEnrolled->exam_date;
              $start_time = $one_courseEnrolled->exam_start;
              $end_time = $one_courseEnrolled->exam_end;
              $status = 'Success';
              echo "
              <tr>
              <td>$courseId</td>
              <td>$section</td>
              <td>$exam_date </td>
              <td>$start_time</td>
              <td>$end_time </td>
              <td>$status </td>
              </tr>";
            }
        

          ?>
          </table>


</div>
            </div>
            </div>
            </div>
            </div>

</div>
         
            </div>

    
            </div>
      <!-- End of Main Content -->


    <!-- </div> -->
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

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








