<?php

require_once '../include/common.php';
// require_once '../include/protect.php';

$round_status_dao = new RoundStatusDAO();
$stop = $round_status_dao->stopRound();

if ( is_array($stop) ) { 
    $result = ["status" => "error",
                "message" => $stop
            ];
}

else {
    $result = ["status" => "success"];
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
 
?>