<?php

require_once '../include/common.php';
require_once '../include/clearingLogic.php';
require_once '../include/protect_json.php';

$round_status_dao = new RoundStatusDAO();
if ($round_status_dao->retrieveCurrentActiveRound() == null) {
    $pre_start_round = null;
}
elseif ($round_status_dao->retrieveCurrentActiveRound()->round_num == 1) {
    roundOneClearing();
}
elseif ($round_status_dao->retrieveCurrentActiveRound()->round_num == 2) {
    roundTwoClearing();
}

$start = $round_status_dao->startRound();

if ( is_array($start) ) { 
    $result = ["status" => "error",
                "message" => $start
            ];
}

else {
    $result = ["status" => "success", 
                "round" => $round_status_dao->retrieveCurrentActiveRound()->round_num
            ];
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT);
 
?>