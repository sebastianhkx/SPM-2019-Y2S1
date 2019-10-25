<?php
require_once 'include/common.php';
require_once 'include/protect.php';

$userid = $_SESSION['userid'];

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
      <li><a href="home_admin.php">Home</a></li>

      <li><a href="bootstrap.php">Start Bootstrap</a></li>
      <li><a href='logout.php'>Log Out</a></li>
    </ul>
  </div>
</nav>

<div class="container">
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
  var_dump($msg['num-record-loaded']);
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
  var_dump($msg);
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
</div> <!--div container ends here-->
</body>
</html>
