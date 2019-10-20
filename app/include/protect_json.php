<?php
require_once 'token.php';
require_once 'common.php';

header('Content-Type: application/json');

$errors = [ isMissingOrEmpty ('token')];
$errors = array_filter($errors);

if (!isEmpty($errors)) {
    $result = [
        "status" => "error",
        "messages" => array_values($errors)
		];
	echo json_encode($result, JSON_PRETTY_PRINT);
	exit();
}
else{
	$token = $_REQUEST['token'];
}
# check if token is not valid
if (verify_token($token)==FALSE){
	echo json_encode(['status'=>'error', 'message'=>['invalid token']],JSON_PRETTY_PRINT);
	exit();
}

?>