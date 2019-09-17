<?php

require_once 'include/common.php';

$dao = new CourseDAO();
$courses = $dao->retrieveAll();

echo "<table border='1'>
    <tr>
        <th>No.</th>
        <th>Course</th>
        <th>School</th>
        <th>Title</th>
        <th>Description</th>
        <th>Exam Date</th>
        <th>Exam Start Time</th>
        <th>Exam End time</th>
        <th>
    </tr>";

for ($i = 1; $i <= count($courses); $i++) {
    $course = $courses[$i-1];
    echo "
    <tr>
        <td>$i</td>
        <td>$course->course</td>
        <td>$course->school</td>
        <td>$course->title</td>
        <td>$course->description</td>
        <td>$course->exam_date</td>
        <td>$course->exam_start</td>
        <td>$course->exam_end</td>
    </tr>";
}

?>