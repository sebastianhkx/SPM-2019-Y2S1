<?php
require_once 'include/common.php';

echo "<h2>Your info:</h2>";

$userid = $_SESSION['userid'];

$student_dao = new StudentDAO();
$student = $student_dao->retrieve($userid);

$bid_dao = new BidDAO();
$bids = $bid_dao->retrieveByUser($userid);

if ( isset($_POST['submit'])) {
    // updates student's info and bids if a new bid was placed
    $student_dao->deductEdollar($userid, $_POST['bidamount']);
    $bidded = new Bid($userid, $_POST['bidamount'], $_POST['course'], $_POST['section']);
    $bid_dao->add($bidded);

    // if place new bid for existing course and section (update bid amout)
    // to do

    // throw errors depending on validation test cases
    // to do
}

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
Course: <input type="text" name="course"> <br>
Section: <input type="text" name="section"> <br>
Bid Amount: <input type="number" name="bidamount"> <br>
<input type="submit" name='submit' value="Confirm Bid">

<br>

    <a href='DisplayCourses.php' target='_blank' >Click to see all courses</a>

</body>
</html>