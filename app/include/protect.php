<?php
require_once 'token.php';
require_once 'common.php';

$userid = '';
if  (isset($_SESSION['userid'])) {
	$userid = $_SESSION['userid'];
}

# check if the username session variable has been set 
# send user back to the login page with the appropriate message if it was not
 
else{
	header("Location: login.php?error=Session not found!");
	// header("Location: login.php");
	exit();
}
	

?>