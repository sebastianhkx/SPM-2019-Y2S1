<?php
require_once 'include/common.php';
require_once 'include/protect.php';

$userid = $_SESSION['userid'];
// protect user page from admin
if ($userid === "admin") {
  header("Location: login.php");
  exit();
}
$student_dao = new StudentDAO();
$bid_dao = new BidDAO();

// Displays the current active round
$roundstatus_dao = new RoundStatusDAO();
$round_status = $roundstatus_dao->retrieveCurrentActiveRound();
if ($round_status != null) {
  $round_num = $round_status->round_num;
  if($round_num == 2){
    header("location:r2bidding.php");
    exit();
  }
}

if (isset($_POST['submitbid'])) {
  $course = $_POST['course']; // for repopulating form fields also
  $section = $_POST['section'];
  $amount = $_POST['bidamount'];

  $bidded = new Bid($userid, $amount, $course, $section);
  $errors = $bid_dao->add($bidded);
}
// get course look up
if ( !empty($_GET['course_lookup']) ) {
  $course_lookup = $_GET['course_lookup'];
  echo "
  <body onload='javascript:filterFunction()'>";

}
else {
  $course_lookup = '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>SB Admin 2 - Dashboard</title>

  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>
<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top" id="mainNav">
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
          <li class="nav-item active">
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

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

        <!-- Begin Page Content -->
        <div class="container-fluid">
          <!-- Content Row -->
          <div class="row">

          <!-- Content Row -->

          <!-- <div class="row"> -->

          <!-- Content Row -->
          <!-- <div class="row"> -->

            <!-- Content Column -->
            <div class="col-lg-5 mb-4">

              <!-- round status -->
              <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">
                    <?php
                    if ($round_status != null) {echo "Current Round: $round_status->round_num";}
                        else {echo "No active bidding round currently";}
                    ?>
                  </h6>
                </div>
              </div>      
              
<!-- new row -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Your Info</h6>
                </div>
                <div class="card-body">
                  <div class="text-center">
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

<!-- row end -->



              </div>


            <div class="col-lg mb-4">

              <!-- current bids -->
              <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Your Current Bids</h6>
                </div>
                <div class="card-body">
                  <div class="text-center">

                  <?php
                $course = '';
                $section = '';
                $amount = '';
              
                $student = $student_dao->retrieve($userid); // student object
                $bids = $bid_dao->retrieveByUser($userid); // could be an array of bids
                $edollar = number_format($student->edollar,2);
              
                echo "<table class='table table-responsive table-bordered'>
                    <tr>
                        <th>No.</th>
                        <th>User ID</th>
                        <th>Amount</th>
                        <th>Course</th>
                        <th>Section</th>
                        <th>Status</th>
                    </tr>";
              
                for ($i = 1; $i <= count($bids); $i++) {
                    $bid = $bids[$i-1];
                    $edollar = number_format($bid->amount,2);
                    echo "
                    <tr>
                        <td>$i</td>
                        <td>$bid->userid</td>
                        <td>$edollar</td>
                        <td>$bid->course</td>
                        <td>$bid->section</td>
                        <td>Pending</td>
                    </tr>";
                }
                echo "</table>";
                ?>

                  </div>
                </div>
              </div>

              <!-- i want to bid for -->
              <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">I want to bid for</h6>
                </div>
                <div class="card-body">

                
                <form action="bidding.php" method="POST">
    <table>
      
  <tr><td style='text-align:left'>Course: </td><td><input type="text" name="course" value="<?= $course ?>" required> </td></tr>
  <tr><td style='text-align:left'>
  Section: </td><td><input type="text" name="section" value="<?= $section ?>" required> </td></tr>
  <tr><td style='text-align:left'>
  Bid Amount: </td><td><input type="number" name="bidamount" placeholder="min 10.00" step="0.01" min="10.00" value="<?= $amount ?>" required>  </td></tr>

  <tr><td><input class="btn btn-primary" type="submit" name='submitbid' value="Confirm Bid"></td></tr>
  
</table>  
  <br>
  <?php
  // if user submits a new bid
  if (isset($errors)) {
    if (is_array($errors)){
      echo "<font color='red'>Error!</font><br><ul>";
      foreach($errors as $err) {
        echo "<font color='red'><li>$err</li></font>";
      }
      echo "</ul>";
    }
    else {
      echo "<font color='green'>Bid was added successfully!<br>
            e$ updated</font><br>";
    
    }
  }
  
  ?>

                </div>
              </div>


              <!-- end of page -->
            </div>
          </div>

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of large row  -->
      <!-- course table goes here  -->
      <?php
      $dao = new CourseDAO();
      $courses = $dao->retrieveAll();
      echo "<h6 class='m-0 font-weight-bold text-primary'>Course Catalogue</h6>";

      ?>
      <input class="form-control bg-light border-1 small" height="100" type="text" id="search_course" onkeyup="filterFunction()" placeholder="Enter course ID to search" value='<?=$course_lookup?>' >

      <?php
      // section lookup

$dao = new SectionDAO();
if (!empty($course_lookup)) {
  $sections = $dao->retrievebyCourse($course_lookup);
  
echo "<h6 class='m-0 font-weight-bold text-primary'>Sections Availble</h6>";

echo "<table class='table table-responsive' id='section' border='1'>
    <tr>
        <th>No.</th>
        <th>Course</th>
        <th>Section</th>
        <th>Day</th>
        <th>Start Time</th>
        <th>End Time</th>
        <th>Instructor</th>
        <th>Venue</th>
        <th>Size</th>
    </tr>";

for ($i = 1; $i <= count($sections); $i++) {
    $section = $sections[$i-1];
    echo "
    <tr>
        <td>$i</td>
        <td>$section->course</td>
        <td>$section->section</td>
        <td>$section->day</td>
        <td>$section->start</td>
        <td>$section->end</td>
        <td>$section->instructor</td>
        <td>$section->venue</td>
        <td>$section->size</td>
    </tr>";
}
echo "</table>";
}
?>

    <table class='table table-responsive' id='course_table' border='1'>
    <tr>
        <th>No.</th>
        <th>Course</th>
        <th>School</th>
        <th>Title</th>
        <th>Description</th>
        <th width='60'>Exam Date</th>
        <th>Exam Start Time</th>
        <th>Exam End time</th>
        
    </tr>
<?php
for ($i = 1; $i <= count($courses); $i++) {
    $course = $courses[$i-1];
    echo "
    <tr>
        <td>$i</td>
        <td>$course->course <br> <a href='bidding.php?course_lookup=$course->course'>See All Sections</a></td>
        <td>$course->school</td>
        <td>$course->title</td>
        <td style='text-align:left'>$course->description</td>
        <td>$course->exam_date</td>
        <td>$course->exam_start</td>
        <td>$course->exam_end</td>
    </tr>";
}
echo "</table>";
?>


    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

 
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
