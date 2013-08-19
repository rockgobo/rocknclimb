<?php
include_once('config.php');

$id = $_GET['id'];

if($id){
    $db = new DB();
    $db->verboseMode = false;
    $db->table = 'boulders_comments';
    $db->addFilter('id', $id);
    $db->limit = 1;
    $db->delete();
}
?>
