<?php
header('Cache-Control: no-cache, must-revalidate');
header('Content-type: application/json');

include_once('config.php');

$type = $_GET['type'] ? $_GET['type'] : 0;
$top = $_GET['top'];
$left = $_GET['left'];
$id = $_GET['id'];
$width = $_GET['width'];
$height = $_GET['height'];
$text = $_GET['text'];

$boulder_id = $_GET['boulder'];
$wall_id = $_GET['wall_id'];

$db = new DB();
$node = new Node();

$newNode = new Node();
$newNode->id = $id;
$newNode->top = $top;
$newNode->left = $left;
$newNode->width = $width;
$newNode->height = $height;
$newNode->text = $text;
$newNode->type = $type;
$newNode->boulder = $boulder_id;
$newNode->wall_id = $wall_id;


$newNode->save();

//Save link between node and boulder
$db->clearQuery();
$db->table = "boulder_nodes";
$db->setParams(array(
        'boulder_id' => $boulder_id,
        'node_id'=> $newNode->id));
$db->save();

echo $newNode->id;
?>