<?php
include_once('config.php');

$boulder_id = $_GET['boulder_id'];
$user_id = $_GET['user_id'];

$db = new DB();
$db->verboseMode = false;
$db->table = 'boulders_completed';
$db->setParams(array(
            'boulderid'=>$boulder_id,
            'userid'=>$user_id,
            'date'=>date('Y-m-d H:i:s')));
$db->insert();
?>