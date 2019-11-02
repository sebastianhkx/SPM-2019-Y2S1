<?php
require_once 'include/common.php';
require_once 'include/protect.php';

$userid = $_SESSION['userid'];
if ($userid !== "admin") {
  header("Location: login.php?error=You are not admin!");
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
  <title>BIOS Bootstrap Output</title>
  <!-- Custom fonts for this template-->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <!-- Custom styles for this template-->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body id="page-top">

<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top" id="mainNav">
    <div class="container">
      <a class="navbar-brand" href="">Merlion University BIOS</a>
      
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

<!-- Page Content start here-->
<div class="container-fluid">
<h1 class="h3 mb-0 text-gray-800">Bootstrap output</h1>

<?php

    require_once 'include/bootstrap.php';
    $msg = doBootstrap();
    $lines_loaded = $msg['num-record-loaded'];
    if (array_key_exists('error', $msg)) {
      $errors = $msg['error'];
    }
    else {
      $errors = [];
    }
    
?>

<table border='1' class='table'>
  <tr>
    <th colspan='2'>Lines Loaded</th>
  </tr>
  <tr>
    <th>File</th>
    <th>Number of lines</th>
  </tr>
<?php
  if(array_key_exists('num-record-loaded',$msg)){
    $sorted_names = [];
    foreach ($msg['num-record-loaded'] as $fileline){
        foreach($fileline as $name=>$num){
            $sorted_names[] = $name;
        }
    }
    sort($sorted_names);
    $sorted_num_record_loaded;
    foreach ($sorted_names as $name){
        foreach ($msg['num-record-loaded'] as $fileline){
          foreach ($fileline as $filename=>$num){
            if ($name === $filename){
              $sorted_num_record_loaded[] = $fileline;
              break;
            }
          } 
        }
    }
    $msg['num-record-loaded'] = $sorted_num_record_loaded;
  }
  //displays lines loaded into a table
  foreach ($lines_loaded as $file_lines){
    foreach ($file_lines as $file=>$line){
      echo "<tr> 
      <td>$file</td>
      <td>$line</td>
      </tr>";
    }
  }
?>
</table>

<hr>


<table border='1' class='table'>
<?php
  //display errors
  if (empty($errors)){
    echo "<tr>
            <th>No Errors!</th>
          </tr>";
  }
  elseif(is_array($errors)){
    if (!is_array($errors[0])){
      echo "{$errors[0]}";
    }
    else{
      echo "<tr>
            <th colspan='3'>Errors</th>
          </tr>
          <tr>
            <th>File</th>
            <th>Line</th>
            <th>Error Message</th>
          </tr>";
      foreach ($errors as $error){
        echo "
              <tr>
                <td>{$error['file']}</td>
                <td>{$error['line']}</td>
                <td>";
        foreach ($error['message'] as $error_msg){
          echo "$error_msg<br>";
        }
          echo "</td>
              </tr>";
      }
    } 
  }


?>
</table>

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