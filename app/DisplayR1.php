<?php

require_once 'include/common.php';

$resultDAO = new ResultDAO();
$results = $resultDAO->retrieveAll();

var_dump($results);

?>

