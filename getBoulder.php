<?php
include_once('config.php');

$id = $_GET['id'];
$user_id = $_GET['user_id'];

$boulder = new Boulder($id);
$boulder->load($user_id);

echo json_encode($boulder);
?>