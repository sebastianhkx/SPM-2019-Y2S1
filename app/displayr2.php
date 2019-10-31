<?php

require_once 'include/common.php';
require_once 'include/protect.php';

$resultDAO = new ResultDAO();
$results = $resultDAO->retrieveByRound(2);

echo "<table border='1'>
    <tr>
        <th>userid</th>
        <th>amount</th>
        <th>course</th>
        <th>section</th>
        <th>result</th>
        <th>round_num</th>
    </tr>";

for ($i = 1; $i <= count($results); $i++) {
    $result = $results[$i-1];
    $edollar = number_format($result->amount,2);
    echo "
    <tr>
        <td>$result->userid</th>
        <td>$edollar</th>
        <td>$result->course</th>
        <td>$result->section</th>
        <td>$result->result</th>
        <td>$result->round_num</th>
    </tr>";
}

echo "</table>";

// var_dump($results);
?>

