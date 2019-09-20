<?php
require_once 'include/common.php';
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
      
      <li class="active"><a href="#">Bidding</a></li>
      <li><a href="#">Page 2</a></li>
      <li><a href='logout.php'>Log Out</a></li>
    </ul>
  </div>
</nav>

<?php
echo "<h2>Your info:</h2>";

$userid = $_SESSION['userid'];

$student_dao = new StudentDAO();
$bid_dao = new BidDAO();

if ( isset($_POST['submit'])){
    if(!isset($_POST['course']) || !isset($_POST['section']) || !isset($_POST['bidamount'])){
        // throw errors depending on validation test cases
        // to do
        //if()
    }
    else{
    $bidded = new Bid($userid, $_POST['bidamount'], $_POST['course'], $_POST['section']);
    //$isUpdated = False;
    //var_dump($bidded);
    $bid_dao->add($bidded);

//     // if student places new bid for existing course and section (update bid amount and refund/deduct e$)
//     foreach ($bids as $bid) {
//         if ($bidded->course == $bid->course && $bidded->section == $bid->section) {
//             $to_refund = $bid->amount - $bidded->amount;
//             $student_dao->addEdollar($userid, $to_refund);
//             $bid->amount = $bidded->amount;
//             $isUpdated = True;
//         }
//     }

//     if ( $isUpdated == False ) {
//         // updates student's info and bids if a new bid was placed
//         $student_dao->deductEdollar($userid, $_POST['bidamount']);
//         $bid_dao->add($bidded);
//     }
    }
}

//$student_dao = new StudentDAO();
$student = $student_dao->retrieve($userid); // student object

//$bid_dao = new BidDAO();
$bids = $bid_dao->retrieveByUser($userid); // could be an array of bids

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

echo "<h2>Your current bids:</h2>";

echo "<table border='1'>
    <tr>
        <th>No.</th>
        <th>User ID</th>
        <th>Amount</th>
        <th>Course</th>
        <th>Section</th>
        <th>Status</th>
        <th>Refund</th>
    </tr>";

for ($i = 1; $i <= count($bids); $i++) {
    $bid = $bids[$i-1];
    echo "
    <tr>
        <td>$i</td>
        <td>$bid->userid</td>
        <td>$bid->amount</td>
        <td>$bid->course</td>
        <td>$bid->section</td>
        <td>Placeholder</td>
        <td>Placeholder</td>
    </tr>";
}

echo "</table>";

?>

<html>
<body>

<br>

<h2>I want to bid for:</h2>
<form action="bidding.php" method="POST">
Course: <input type="text" name="course" required> <br>
Section: <input type="text" name="section" required> <br>
Bid Amount: <input type="number" name="bidamount" required> <br>
<input type="submit" name='submit' value="Confirm Bid" >

<br>

    <a href='DisplayCourses.php' target='_blank' >Click to see all courses</a>

<br>   
    <a href='logout.php'>Log Out</a>
</body>
</html>