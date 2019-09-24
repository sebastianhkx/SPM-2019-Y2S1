<?php


$username = $_POST['userid'];
$password = $_POST['password'];

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

echo $passwordHash;

?>