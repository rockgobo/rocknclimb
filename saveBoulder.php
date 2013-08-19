<?php
header('Cache-Control: no-cache, must-revalidate');
header('Content-type: application/json');

include_once('config.php');

$id = $_GET['id'];
$name = $_GET['name'];
$description = $_GET['description'];
$wall = $_GET['wall'];
$user_id = $_GET['user_id'];
$isRemix = $_GET['isRemix'];

$step_grip = $_GET['step_grip']?$_GET['step_grip']:false;
$step_spax = $_GET['step_spax']?$_GET['step_spax']:true;

$newBoulder = new Boulder();
$newBoulder->Id = $id;
$newBoulder->Name = $name;
$newBoulder->Description = $description;
$newBoulder->WallId = $wall;
$newBoulder->UserId = $user_id;
$newBoulder->StepGrip = $step_grip == "true"?true:false;
$newBoulder->StepSpax = $step_spax == "true"?true:false;
$newBoulder->IsRemix = $isRemix == "true"?true:false;

$newBoulder->save();

echo $newBoulder->Id;
?>