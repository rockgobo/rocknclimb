<?php

include_once("config.php");

$facebook  = $_GET['facebook'];
$facebook_id  = $_GET['facebook_id'];
$facebook_username  = $_GET['facebook_username'];
$username  = $_GET['username'];
$password  = $_GET['password'];
$id        = $_GET['id'];



$user = new User();
if($id) { 
    $user->id = $id; 
}
if($facebook)   
{ 
    $user->facebook_name = $facebook;
    $user->username = $facebook;
    $user->facebook_id = $facebook_id;
    $user->facebook_username = $facebook_username; 
}
if($username)   { 
    $user->username = $username; 
}
$user->load();

if(!$user->isValid($password)){
    return;
}

$user->save();

echo json_encode($user);
?>