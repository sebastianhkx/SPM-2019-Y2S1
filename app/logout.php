<?php
require_once 'include/common.php';
require_once 'include/protect.php';

session_destroy();

header("Location: login.php");
exit;
?>



