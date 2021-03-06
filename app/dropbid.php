<?php
    require_once 'include/common.php';
    require_once 'include/protect.php';
    $roundstatus_dao = new RoundStatusDAO();
    $student_dao = new StudentDAO();
    $bid_dao = new BidDAO();
    $round_status = $roundstatus_dao->retrieveCurrentActiveRound();
    $round_message = "No active bidding round currently.";
    $errors = "";
    $userid = $_SESSION['userid'];
    $disabled = "";
    $page = "bidding.php";
    // protect user drop bid page from admin
    if($userid==='admin'){
      header("Location:home_admin.php");
      exit();
    }

    if(isset($_POST['submitdrop']) && isset($_POST['drop_course'])){
      $coursedrop = $_POST['drop_course'];
      $selected_bid = $bid_dao->retrieveByUseridCourse($userid, $coursedrop);
      $bid_to_drop_temp = new Bid($userid, 0, $coursedrop, $selected_bid->section); // the current drop bid method doesn't need amount. might need to revisit the method.
      $bid_to_drop = new Bid($userid, $bid_dao->checkExistingBid($bid_to_drop_temp), $coursedrop, $selected_bid->section);
      $errors = $bid_dao->drop($bid_to_drop);
    }

    if($round_status != null){
        if($round_status->round_num == 1){
            $page = "bidding.php";
        }
        else{
            $page = "r2bidding.php";
        }
        $round_message = "Current Round: Round $round_status->round_num";
    }else{
        $disabled = "disabled";
    }
    
    $student = $student_dao->retrieve($userid);//student object
    $bids = $bid_dao->retrieveByUser($userid); //array of bids object
    $edollar = number_format($student->edollar,2);

    $table = "<table class='table table-responsive-ml table-bordered table-hover'>
                <tr>
                  <th>No.</th>
                  <th>Amount</th>
                  <th>Course</th>
                  <th>Section</th>
                  <th>Drop</th>
                </tr>";
    $count = 0;
    foreach($bids as $bid){
      $count++;
      $dollar = number_format($bid->amount,2);
      $table .= "<tr>
                    <td>$count</td>
                    <td>$dollar</td>
                    <td>{$bid->course}</td>
                    <td>{$bid->section}</td>
                    <td> <input type='radio' name='drop_course' value={$bid->course} $disabled> </td>
                </tr>";
    }
    $table .= "<tr><td colspan='5'><input class='btn btn-primary' type='submit' name='submitdrop' value='Drop Bid' $disabled>
              </td></tr>
              </table>";
?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>BIOS Drop Bid</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
  </head>

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
        <li class="nav-item">
          <a class="nav-link" href=<?=$page?>>Bidding&nbsp;</a>
        </li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item active">
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
  </nav>

  <body>
    <div id = 'wrapper'>
      <div class = 'container-fluid'>
        <div class ='row mt-4'>

          <div class='col-md-4'>
            <!-- first card in left column -->
            <div class='card shadow mb-4'>
              <div class='card-body'>
                <h5 class='m-0 font-weight-bold text-primary'>
                  <?=$round_message?>
                </h5>
              </div>
            </div>
            <!-- second card in left column -->
            <div class='card shadow mb-4'>
              <div class ='card-header'>
                <h6 class='m-0 font-weight-bold text-primary'>Your Info</h6>
              </div>
              <div class='card-body text-center'>
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
                    <td><?=$edollar?></td>
                  </tr>
                </table>
              </div>
            </div>
          </div>

          <div class='col'>
            <div class='card shadow mb-4'>
              <div class='card-header'>
                <h5 class='m-0 font-weight-bold text-primary'>I want to drop:</h5>                
              </div>
              <div class='card-body text-center'>
                <form method='POST' action ='dropbid.php '>
                  <?=$table?>
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
                    echo "<font color='green'>Bid was dropped successfully!<br>
                          e$ updated</font>";
                  }
                ?>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

  </body>
</html>