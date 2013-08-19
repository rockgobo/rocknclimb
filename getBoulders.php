<?php
include_once('config.php');

$wall_id = $_GET["wall_id"] ? $_GET["wall_id"] : false;
$user_id = $_GET["user_id"] ? $_GET["user_id"] : false;

$ids = array();
$db = new DB();
$db->verboseMode = false;
$db->table = "boulders";
$db->setParams("id");
if($wall_id){
    $db->addFilter("wall_id", $wall_id);
}
$db->addFilter("deleted", 0);
$db->select();

while (list($id) = $db->fetchRow())
{
    array_push($ids,$id);
}

$first = false;
$boulders = array();
foreach($ids as $id){
    $boulder = new Boulder($id);
    if($user_id){
        $boulder->load($user_id);
    }
    else{
        $boulder->load();
    }
    array_push($boulders, $boulder);
}

echo '{"boulders" : '.json_encode($boulders) . '}';
?>