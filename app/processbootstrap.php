<?php
require_once 'include/common.php';

// implement protect.php later

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
    $errors = $msg['error'];
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
    if (sizeof($errors)==1){
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
