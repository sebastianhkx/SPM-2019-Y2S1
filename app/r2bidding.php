<?php
  require_once 'include/common.php';
  require_once 'include/protect.php';
  require_once 'include/clearingLogic.php';

  $userid = $_SESSION['userid'];

  $student_dao = new StudentDAO();
  $bid_dao = new BidDAO();
  $course_dao = new CourseDAO();
  $section_dao = new SectionDAO();
  $r2Bid_dao = new R2BidDAO();
  $roundstatus_dao = new RoundStatusDAO();
  //$courses = $course_dao->retrieveAll();
  $sections = $section_dao->retrieveAll();
  $round_status = $roundstatus_dao->retrieveCurrentActiveRound();
  $round_message = "Current Round: Round $round_status->round_num";
  $errors = "";
  $section_info = "Search a module to check the available seats and minimum bid amount";
  // protect r2bidding page 
  if($round_status->round_num == 1){
    header('location:bidding.php');
    exit();
  }elseif($userid === 'admin'){
    header('location:home_admin.php');
  }



  if(isset($_POST['submit_bid'])){
    $amount = $_POST['bidamount'];
    $select_course = $_POST['course'];
    $select_section = $_POST['section'];
    $bid = new Bid($userid,$amount,$select_course,$select_section);
    $errors = $bid_dao->add($bid);
  }
  $student = $student_dao->retrieve($userid);
  $student_edollar = number_format($student->edollar,2);


  $bids = $bid_dao->retrieveByUser($userid);
  $table = "";
  $num = 0;
  foreach($bids as $bid){
    $num ++;
    $bid_statues = $bid_dao->bidStatus($bid);
    $edollar = number_format($bid->amount,2);
    $r2BidInfo = $r2Bid_dao->getr2bidinfo($bid);
    $vacancy = $r2BidInfo->vacancy;
    $clearingPrice = $bid_dao->getRoundTwoSuccessfullPrice($bid, $vacancy);
    if ($bid->amount>$clearingPrice){
      $result = 'Success';
    }
    else{
      $result = 'Fail';
    }
    $table .= "<tr>
                <td>$num</td>
                <td>{$bid->course}</td>
                <td>{$bid->section}</td>
                <td>{$edollar}</td>
                <td>$result</td>
              </tr>";
  }

?>

<!DOCTYPE html>
<html lang='en'>

  <head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>BIOS R2 Bidding</title>

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
          <li class="nav-item">
            <a class="nav-link" href="home.php">Home&nbsp;</a>
          </li>
        </ul>
        <ul class="navbar-nav">
          <li class="nav-item  active">
            <a class="nav-link" href="r2bidding.php">Bidding&nbsp;</a>
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

      <!-- Page Wrapper -->
    <div id='wrapper'>

      <div class='container-fluid mt-4'>
        <!-- first row -->
        <div class='row mt-4"'>

          <div class='col-md-4'>
            <!-- first card -->
            <div class='card shadow mb-4'>
              <div class='card-body'>
                <h5 class='m-0 font-weight-bold text-primary'>
                  <?=$round_message?>
                </h5>
              </div>
            </div>
            <!-- second card -->
            <div class='card shadow mb-4'>
              <div class ='card-header'>
                <h6 class='m-0 font-weight-bold text-primary'>Your Info</h6>
              </div>
              <div class='card-body'>
                <div class='text-center'>

                  <table class='table table-bordered'>
                    <tr>
                      <th>Name</th>
                      <td><?=$student->name?></td>
                    </tr>
                    <tr>
                      <th>School</th>
                      <td><?=$student->school?></td>
                    </tr>
                    <tr>
                      <th>e$ Balance</th>
                      <td><?=$student_edollar?></td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
            <!-- end of left column -->
          </div>

          <div class='col'>
            <!-- first card -->
            <div class='card shadow mb-4'>
              <div class='card-header'>
                <h5 class='m-0 font-weight-bold text-primary'>Your Current Bids for Round 2:</h5>
              </div>
              <div class='card-body text-center'>
                <table class='table table-bordered table-responsive-ml'>
                  <tr>
                    <th>No.</th>
                    <th>Course</th>
                    <th>Section</th>
                    <th>Amount</th>
                    <th>Live Status</th>
                  </tr>
                  <?=$table?>
                </table>
              </div>
            </div>

            <!-- second card -->
            <div class='card shadow mb-4'>
              <div class='card-header'>
                <h5 class='m-0 font-weight-bold text-primary'>I want to Bid:</h5>
              </div>
              <div class='card-body'>
                <form method='POST' action='r2bidding.php'>
                  <table class='table table-bordered table-responsive-ml'>
                    <tr>
                      <th>Course</th>
                      <td><input type="text" name="course" required></td>
                    </tr>
                    <tr>
                      <th>Section</th>
                      <td><input type="text" name="section" required></td>
                    </tr>
                    <tr>
                      <th>Bid Amount</th>
                      <td><input type="number" name="bidamount" placeholder="min 10.00" step="0.01" min="10.00" required></td>
                    </tr>
                    <tr><td colspan='2'><input class='btn btn-primary' type="submit" name='submit_bid' value="Bid" ></td></tr>
                  </table>
                </form>
                <?php
                  if(is_array($errors)){
                    echo "<font color='red>Error!</font><br>!";
                    echo "<ul>";
                    foreach($errors as $err){
                      echo "<font color='red'><li>$err</li></font>";
                    }
                    echo "</ul>";
                  }elseif($errors != ''){
                    echo "<font color='green'>Bid was added successfully!<br>
                          e$ updated</font>";
                  }
                ?>
              </div>
            </div>
            <!-- end of right column -->
          </div>
        <!-- end of first row -->
        </div>

        <!-- second row -->
        <div class='row'>
          <div class='col'>
            <h6 class='m-0 font-weight-bold text-primary'>Search a module to check the available seats and minimum bid amount</h6>
            <input class='form-control bg-light border-1' type='text' id='search_course' onkeyup="filterFunction()" placeholder='Enter Course ID'>
            <table class='table table-bordered table-responsive-ml' id='course_table'>
              <tr>
                <th>No.</th>
                <th>Course</th>
                <th>Section</th>
                <th>School</th>
                <th>Title</th>
                <th>Vacancy</th>
                <th>Minimum Bid</th>
              </tr>
              <?php
                $count = 0;
                foreach($sections as $section){
                  $count ++;
                  $course = $course_dao->retrieveByCourseId($section->course);
                  $temp_bid = new Bid($userid, 10, $section->course, $section->section);
                  $r2Info = $r2Bid_dao->getr2bidinfo($temp_bid);
                  echo "<tr>
                          <td>$count</td>
                          <td>$section->course</td>
                          <td>$section->section</td>
                          <td>$course->school</td>
                          <td>$course->title</td>
                          <td>{$r2Info->vacancy}</td>
                          <td>{$r2Info->min_amount}</td>
                        </tr>";
                }
              ?>
            </table>
          </div>
        </div>
        <!-- end of second row -->

      </div>
    </div>


  <!-- // search course script -->
  <script>  

    function filterFunction() {
      // Declare variables
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("search_course");
      filter = input.value.toUpperCase();
      table = document.getElementById("course_table");
      tr = table.getElementsByTagName("tr");

      // Loop through all table rows, and hide those who don't match the search query
      for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[1];
        if (td) {
          txtValue = td.textContent || td.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }
      }
    }

  </script>

  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin-2.min.js"></script>

  <!-- Page level plugins -->
  <script src="vendor/chart.js/Chart.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="js/demo/chart-area-demo.js"></script>
  <script src="js/demo/chart-pie-demo.js"></script>

  </body>
</html>