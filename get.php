<?php
include_once('config.php');
$id = $_GET('id');
$db = new DB();
$db->verboseMode = true;
$db->table = "Nodes";
$db->setParams(array('id','top','Nodes.left','width','height','Nodes.text','Nodes.type'));
$db->addFilter("boulder_id", 1);
$db->select();

$first = false;
echo '[';
while(list($id,$top,$left,$width,$height,$text,$type) = $db->fetchRow()){
    if($first)
    {
    echo ',';
    }
    echo '{ "top": '.$top.', "left": '.$left.', "width": '.$width.', "height": '.$height.', "text": "'.$text.'", "id": "'.$id.'", "editable": true, "type": "'.$type.'" }';
    $first = true;
}
echo ']';

?>