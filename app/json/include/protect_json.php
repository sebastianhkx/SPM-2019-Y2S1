<?php
require_once 'token.php';
require_once 'common.php';

$token = '';
if  (isset($_REQUEST['token'])) {
	$token = $_REQUEST['token'];
}

# check if token is not valid
if (verify_token($token)==FALSE){
	echo "token authentication failed";
	exit();
}
?>