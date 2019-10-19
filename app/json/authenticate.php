<?php

require_once '../include/common.php';
require_once '../include/token.php';
// require_once '../include/protect.php';


// isMissingOrEmpty(...) is in common.php
$errors = [ isMissingOrEmpty ('username'), 
            isMissingOrEmpty ('password') ];
$errors = array_filter($errors);


if (!isEmpty($errors)) {
    $result = [
        "status" => "error",
        "messages" => array_values($errors)
        ];
}
else{
    $userid = $_POST['username'];
    $password = $_POST['password'];

    # check if userid and password are right. generate a token and return it in proper json format

    # generate a secret token for the user based on their userid

    # return the token to the user via JSON    
		
    # return error message if something went wrong
    $dao = new AdminDAO();
    $user = $dao->retrieve($userid);

    if ( $user != null && $user->authenticate($password) ) { 
        $result = ["status"=>"success", 
                    "token"=>generate_token($userid)
                ];
    } 
    else {
        $result = ["status" => "error", 
                    "messages" => ['invalid userid/password']
                ];
    }
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
 
?>