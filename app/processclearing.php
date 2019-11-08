<?php
// backend file
require_once 'include/common.php';
// require_once 'include/clearingLogic.php';
require_once 'include/protect.php';

$userid = $_SESSION['userid'];

$roundstatus_dao = new RoundStatusDAO();

if (isset($_POST['stop_r1'])) {
  $roundstatus_dao->stopRound();
  // roundOneClearing();
  //stop round now automatically triggers roundoneclearing
  header("Location: home_admin.php");
  exit;
}

elseif (isset($_POST['stop_r2'])){
  $roundstatus_dao->stopRound();
  // roundTwoClearing();
  // stop round now automatically triggers roundtwoclearing
  header("Location: home_admin.php");
  exit;
}

elseif (isset($_POST['start_r1']) || isset($_POST['start_r2'])) {
  $roundstatus_dao->startRound();
  header("Location: home_admin.php");
  exit;
}

    // roundOneClearing();
    
?>

