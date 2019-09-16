<?php
require_once 'include/common.php';

echo "Your info<br>";

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

    <tr><th>E-dollar Balance</th>
    <td>
    $result->edollar
    </td></tr>

    </table><br>";

echo "Your current bids<br>";

$bid_dao = new BidDAO();
$bids = $bid_dao->retrieveByUser($userid);

echo "<table border='1'>
    <tr>
        <th>No.</th>
        <th>User ID</th>
        <th>Amount</th>
        <th>Course</th>
        <th>Section</th>
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
    </tr>";
}

echo "</table>";


echo "<a href='DisplayCourses.php' target='_blank' >Click to see all courses</a>";
?>