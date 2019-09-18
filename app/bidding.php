<?php
require_once 'include/common.php';

echo "<h2>Your info:</h2>";

$userid = $_SESSION['userid'];

$dao = new StudentDAO();
$result = $dao->retrieve($userid);

echo "<table border=1>
    <tr>
        <th>Name</th>
    <td>
    $result->name
    </td></tr>  
    <tr><th>School</th>
    <td>
    $result->school
    </td></tr>

    <tr><th>e$ Balance</th>
    <td>
    $result->edollar
    </td></tr>

    </table><br>";

echo "<h2>Your current bids:</h2>";

$bid_dao = new BidDAO();
$bids = $bid_dao->retrieveByUser($userid);

echo "<table border='1'>
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
    echo "
    <tr>
        <td>$i</td>
        <td>$bid->userid</td>
        <td>$bid->amount</td>
        <td>$bid->code</td>
        <td>$bid->section</td>
        <td>Placeholder</td>
    </tr>";
}

echo "</table>";

?>

<html>
<body>

<br>

<h2>I want to bid for:</h2>
<form action="process_bids.php" method="POST">
Course: <input type="text" name="Course"> <br>
Section: <input type="text" name="Section"> <br>
Bid Amount: <input type="text" name="Bid Amount"> <br>

<br>

    <a href='DisplayCourses.php' target='_blank' >Click to see all courses</a>

</body>
</html>