<!-- testing of auto submit form -->

<form method="post" action='test.php'>
    <select name="myselect" onchange="this.form.submit();">
        <option>blue</option>
        <option>red</option>
    </select>
</form>

<?php

$selected = '';

$res = $_POST['myselect'];

echo $res;

?>