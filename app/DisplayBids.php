<?php

require_once 'include/common.php';

$dao = new BidDAO();
$bids = $dao->retrieveAll();

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

?>