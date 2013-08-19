<?php
include_once('config.php');

$ids = array();
$db = new DB();
$db->verboseMode = false;
$db->table = "walls";
$db->setParams("id");
$db->select();

while (list($id) = $db->fetchRow())
{
    array_push($ids,$id);
}

$walls = array();
foreach($ids as $id){
    $wall = new Wall($id);
    $wall->load();
    array_push($walls, $wall);
}

echo json_encode($walls);
?>