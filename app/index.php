<?php
require_once 'include/common.php';

if ( isset($_SESSION['username']) ) {
    if ($_SESSION == "admin") {
    header("Location: home_admin.php");
    }

    else {
        header("Location: home.php");
    }
}

else {
    header("Location: login.php");
}
exit;
?>
