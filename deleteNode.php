<?php
include_once('config.php');

$id = $_GET['id'];


if($id){
    $db = new DB();
    $db->table = 'nodes';
    $db->addFilter('id', $id);
    $db->delete();
}
?>
