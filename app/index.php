<?php
require_once 'include/common.php';

if ( isset($_SESSION['username']) ) {
    header("Location: home.php");
}

else {
    header("Location: login.php");
}
exit;
?>
