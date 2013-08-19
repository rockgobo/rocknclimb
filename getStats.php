<?php
include_once('config.php');

$db = new DB();
$db->verboseMode = false;
$db->query("SELECT count(*) from boulders where deleted = 0");

$result='{"boulder_count": ';
if($count = $db->fetchFirst()){
    $result .= $count;
}
else{
    $result .= '0';
}
$result .= ', "user_count": ';

$db->query("SELECT count(*) from users");

if($count = $db->fetchFirst()){
    $result .= $count;
}
else{
    $result .= '0';
}
$result .= '}';

echo $result;
?>