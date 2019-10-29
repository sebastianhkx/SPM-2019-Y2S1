<?php

require_once '../include/common.php';
require_once '../include/token.php';

## testing comment out
// require_once '../include/protect.php';

// isMissingOrEmpty(...) is in common.php
$errors = [ isMissingOrEmpty ('password'),
            isMissingOrEmpty ('username')
             ];
$errors = array_filter($errors);


if (!isEmpty($errors)) {
    $result = [
        "status" => "error",
        "message" => array_values($errors)
        ];
}
else{
    $username = $_POST['username'];
    $password = $_POST['password'];

    # check if userid and password are right. generate a token and return it in proper json format
    # generate a secret token for the user based on their userid
    # return the token to the user via JSON    
    # return error message if something went wrong
    $dao = new AdminDAO();
    $user = $dao->retrieve($username);

    $invalid_errors = [];
    if ($user == null) {
        $invalid_errors[] = "invalid username";
    }
    elseif ($user->authenticate($password) == false) {
        $invalid_errors[] = "invalid password";
    }

    if ( empty($invalid_errors) ) { 
        $result = ["status"=>"success", 
                    "token"=>generate_token($username)
                ];
    } 
    else {
        $result = ["status" => "error", 
                    "message" => $invalid_errors
                ];
    }
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
 
?>