<?php
header('Cache-Control: no-cache, must-revalidate');
header('Content-type: application/json');

include_once('config.php');

$boulder_id = $_GET['boulderid'];
$comment = $_GET['comment'];
$rating = $_GET['rating']?$_GET['rating']:0;
$difficulty = $_GET['difficulty']?$_GET['difficulty']:0;
$user_id = $_GET['user_id'];

$updated = false;

if($boulder_id){
    $db = new DB();  
    $db->verboseMode = false;
    $db->table = "boulders_comments";
    
    if($user_id && ($difficulty > 0 || $rating > 0)){
        $db->setParams(array("user_id","rating","difficulty"));
        $db->setFilter("boulder_id='".$boulder_id."' AND user_id='".$user_id."' AND ( difficulty > 0 OR rating > 0)" );
        $db->select();
        
        if(list($db_user_id, $db_rating, $db_difficulty) = $db->fetchRow()){
            $db->setParams(
                array(
                    "comment"=>$comment,
                    "rating"=>$rating,
                    "difficulty"=>$difficulty
                )
            );
            $db->update();
            $updated = true;
        }
    }
    
    if(!$updated){
        $db->setParams(
            array(
                "boulder_id"=>$boulder_id,
                "comment"=>$comment,
                "rating"=>$rating,
                "difficulty"=>$difficulty,
                "user_id"=>$user_id
            )
        );
        $db->insert();
    }
}
?>