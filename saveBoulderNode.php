<?php
$boulder_id = $_GET['boulder_id'];
$node_id = $_GET['node_id'];

$db = new DB();
$db->verboseMode = false;
$db->table = "boulder_nodes";
$db->setParams(array(
    'boulder_id',
    'node_id'));
$db->addFilter('boulder_id', $boulder_id);
$db->addFilter('node_id', $node_id);
$db->select();

if(list($boulder_id, $node_id) = $db->fetchRow()){
    $db->delete();
}
else{
    $db->setParams(array(
        'boulder_id' => $boulder_id,
        'node_id'=> $node_id));
    $db->save();
}
?>