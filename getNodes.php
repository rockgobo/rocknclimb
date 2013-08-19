<?php
include_once('config.php');

$id =  $_GET['boulder_id'];


$db = new DB();
$db->table = "nodes n, boulder_nodes bn";
$db->verboseMode = false;
$db->setParams(array("n.id","n.top","n.left","n.width","n.height","n.text","n.type"));
$db->setFilter("bn.boulder_id = ".  $id ." AND bn.node_id = n.id");
$db->select();

$first = false;
echo '{"nodes" : [';
while(list($id,$top,$left,$width,$height,$text,$type) = $db->fetchRow()){
    if($first)
    {
        echo ',';
    }
    echo '{ "top": '.$top.', "left": '.$left.', "width": '.$width.', "height": '.$height.', "text": "'.$text.'", "id": "'.$id.'", "type": '.$type.' }';
    $first = true;
}
echo ']}';
?>