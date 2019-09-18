<?php

require_once 'include/common.php';

$dao = new SectionDAO();
$sections = $dao->retrieveAll();

echo "<table border='1'>
    <tr>
        <th>No.</th>
        <th>Course</th>
        <th>Section</th>
        <th>Day</th>
        <th>Start Time</th>
        <th>End Time</th>
        <th>Instructor</th>
        <th>Venue</th>
        <th>Size</th>
    </tr>";

for ($i = 1; $i <= count($sections); $i++) {
    $section = $sections[$i-1];
    echo "
    <tr>
        <td>$i</td>
        <td>$section->course</td>
        <td>$section->section</td>
        <td>$section->day</td>
        <td>$section->start</td>
        <td>$section->end</td>
        <td>$section->instructor</td>
        <td>$section->venue</td>
        <td>$section->size</td>
    </tr>";
}

?>