<?php
include_once("config.php");

$username  = $_GET['username'];
$password  = $_GET['password'];

$user = new User();

$user->username = $username;
$user->password = $password;

$user->facebook_id = 0;
$user->facebook_username = $facebook_username;

if($user->checkUsername()){
    $user->save();
    echo json_encode($user);
}
?>