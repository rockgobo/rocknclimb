<?php
include_once("config.php");

$id       = $_GET['id'];
$private  = $_GET['private'] == 'true'? 1 : 0;




if($id){ 
    $user = new User();
    $user->id = $id;
    $user->load();

    $user->private = $private;
    $user->save();
}
?>