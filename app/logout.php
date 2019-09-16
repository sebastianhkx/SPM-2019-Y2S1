<?php
require_once 'include/common.php';

// implement protect.php later

session_destroy();

header("Location: login.php");
exit;
?>



