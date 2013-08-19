<?php
include_once('config.php');

$db = new DB();
$db->table = "news";
$db->verboseMode = false;
$db->setParams(array("bouldername","wallname","username","type","id","wall_id","date"));
$db->setLimit(50);
$db->select();

$first = false;
echo '[';
while(list($bouldername,$wallname,$username,$type,$id,$wall_id,$date) = $db->fetchRow()){
    //Convert datetime
    $datetime = date("d.m.Y", strtotime($date));

    if($first)
    {
        echo ',';
    }
    echo '{ "bouldername": "'.$bouldername.'", "wallname": "'.$wallname.'", "username": "'.$username.'", "type": '.$type.', "boulder_id": '.$id.', "wall_id": '.$wall_id.', "date": "'.$datetime.'" }';
    $first = true;
}
echo ']';
?>