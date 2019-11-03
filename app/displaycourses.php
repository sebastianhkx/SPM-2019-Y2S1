<?php

require_once 'include/common.php';
require_once 'include/protect.php';
?>
<!-- <html>
    <style>
        table {
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid black;
            text-align: center;
        }

        th {
        padding: 10px;
        }
    </style>
</html> -->
<?php
$dao = new CourseDAO();
$courses = $dao->retrieveAll();

// to be implemented
// filter by school
// filter by subject
// search course
// 
?>

<html>
<input height="100" type="text" id="search_course" onkeyup="filterFunction()" placeholder="Search for course" >

<?php
echo "<table id='course_table' border='1'>
    <tr>
        <th>No.</th>
        <th>Course</th>
        <th>School</th>
        <th>Title</th>
        <th>Description</th>
        <th width='60'>Exam Date</th>
        <th>Exam Start Time</th>
        <th>Exam End time</th>
        
    </tr>";

for ($i = 1; $i <= count($courses); $i++) {
    $course = $courses[$i-1];
    echo "
    <tr>
        <td>$i</td>
        <td>$course->course</td>
        <td>$course->school</td>
        <td>$course->title</td>
        <td style='text-align:left'>$course->description</td>
        <td>$course->exam_date</td>
        <td>$course->exam_start</td>
        <td>$course->exam_end</td>
    </tr>";
}
echo "</table>";
?>

<script>
function filterFunction() {
  // Declare variables
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("search_course");
  filter = input.value.toUpperCase();
  table = document.getElementById("course_table");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}
</script>

