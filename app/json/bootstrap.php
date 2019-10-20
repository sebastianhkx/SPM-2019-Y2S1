<?php

// require_once '../include/protect_json.php';
require_once '../include/bootstrap.php';
//dont need common.php as bootstrap.php already require_once it

$msg = doBootstrap();

header('Content-Type: application/json');
echo json_encode($msg, JSON_PRETTY_PRINT);

?>