<?php
require_once 'token.php';
require_once 'common.php';


$token = '';
header('Content-Type: application/json');
if  (isset($_REQUEST['token'])) {
	//token present
	$token = $_REQUEST['token'];
	if ($token == ''){
		//token blank
		echo json_encode(['status'=>'error', 'message'=>['blank token']],JSON_PRETTY_PRINT);
		exit();
	}
	# check if token is not valid
	elseif (verify_token($token)==FALSE){
		echo json_encode(['status'=>'error', 'message'=>['invalid token']],JSON_PRETTY_PRINT);
		exit();
	}
}
else{
	//token missing
	echo json_encode(['status'=>'error', 'message'=>['missing token']],JSON_PRETTY_PRINT);
	exit();
}

?>