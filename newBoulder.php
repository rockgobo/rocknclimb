<?php
include_once('config.php');

$ids = array();
$db = new DB();
$db->verboseMode = false;
$db->table = "settings";
$db->setParams("value");
$db->addFilter("settings_key","next_project");
$db->select();

if ($value = $db->fetchFirst())
{
  $db->setParams(array("value"=>$value+1));
  $db->update();
  echo $value;
}
?>