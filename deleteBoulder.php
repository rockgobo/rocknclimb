<?php
include_once('config.php');

$id = $_GET['id'];


if($id){
    $db = new DB();
    $db->table = 'boulders';
    $db->addFilter('id', $id);
    $db->setParams(array("deleted"=>1));
    $db->update();
}
?>