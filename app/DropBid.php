<?php
require_once 'include/common.php';
$userid = $_SESSION['userid'];
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
        <li class="active"><a href='dropbid.php'>Drop Bid</a></li>
        <li><a href='logout.php'>Log Out</a></li>
    </ul>
    </div>
</nav>
<?php

    $student_dao = new StudentDAO();
    $bid_dao = new BidDAO();
    $bids = $bid_dao->retrieveByUser($userid);

    if(isset($_POST['submit'])){
        if(isset($_POST['bid'])){
            $index = $_POST['bid'];
            $bid_drop = $bids[$index-1];
            //var_dump($bid_drop);
            $bid_dao->drop($bid_drop);
            $student_dao->addEdollar($bid_drop->userid, $bid_drop->amount);
        }
    }

    
    $student = $student_dao->retrieve($userid); // student object

    
    $bids = $bid_dao->retrieveByUser($userid); // could be an array of bids

    echo "<h2>Your info:</h2>";
    echo "<table border=1>
        <tr>
            <th>Name</th>
            <td>$student->name</td>
        </tr>  
        <tr>
            <th>School</th>
            <td>$student->school</td>
        </tr>
        <tr>
            <th>e$ Balance</th>
            <td>$student->edollar</td>
        </tr>
        </table><br>";
?>

<html>
<body>
<form action="dropBid.php" method="POST">
<h2>Your current bids:</h2>

<table border='1'>
    <tr>
        <th>No.</th>
        <th>User ID</th>
        <th>Amount</th>
        <th>Course</th>
        <th>Section</th>
        <th>Drop</th>
    </tr>

<?php
    for ($i = 1; $i <= count($bids); $i++) {
        $bid = $bids[$i-1];
        echo "
        <tr>
            <td>$i</td>
            <td>$bid->userid</td>
            <td>$bid->amount</td>
            <td>$bid->course</td>
            <td>$bid->section</td>
            <td style='text-align:center'><input type='radio' name='bid' value='$i'></td>
        </tr>";
    }
?>
</table>

    <br>
    <input type="submit" name='submit' value="Confirm Drop" >
    <br>
        <a href='displayCourses.php' target='_blank' >Click to see all courses</a>

    <br>  
    <a href='logout.php'>Log Out</a> 
</body>
</html>