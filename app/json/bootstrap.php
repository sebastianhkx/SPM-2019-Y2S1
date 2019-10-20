<?php

// require_once '../include/protect_json.php';
require_once '../include/bootstrap.php';
//dont need common.php as bootstrap.php already require_once it

$msg = doBootstrap();

if(array_key_exists('error',$msg)){
    $errors = $msg['error'];
    $sort_errors = [];
    $files_name = $msg['num-record-loaded'];

    for($i=0;$i<sizeof($files_name);$i++){
        $file = array_keys($files_name[$i])[0];
        foreach($errors as $err){
            if($err['file'] == $file){
                $sort_errors[] = $err;
            }
        }
    }
    $msg['error'] = $sort_errors;
}

header('Content-Type: application/json');
echo json_encode($msg, JSON_PRETTY_PRINT);

?>