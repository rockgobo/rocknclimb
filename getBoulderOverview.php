<?php
include_once('config.php');

$user_id = $_GET[user_id]?$_GET[user_id]:false;

$db = new DB();

//Get finished Boulder by user
$finished_boulders = array();
if($user_id){
    $db->query('SELECT
    boulderid
    FROM boulders_completed  
    WHERE userid='.$user_id);

    while(list($boulder_id) = $db->fetchRow()){
        array_push($finished_boulders, $boulder_id);
    }
}

$db->query('SELECT
    w.id as wall_id,
    b.id as boulder_id,
    w.name as wall_name, 
    b.name as boulder_name, 
    b.created,
    count(bc.userid) as count_finished, 
    ifnull(avg(bct.rating),0) as average_rating, 
    ifnull(avg(bct.difficulty),0) as average_difficulty,
    ifnull(max(bct.rating),0) as max_rating,
    ifnull(max(bct.difficulty),0) as max_difficulty
FROM walls w JOIN boulders b on w.id = b.wall_id
    LEFT OUTER JOIN boulders_completed bc on b.id = bc.boulderid 
    LEFT OUTER JOIN boulders_comments bct on bct.boulder_id = b.id 
    WHERE b.deleted = 0
    GROUP BY b.id
    ORDER BY wall_id, average_difficulty, count_finished DESC');

    
    
$first_boulder = false; 
$first_wall = false;
$last_wall = 0;
echo('[');    
while(list($wall_id,$boulder_id, $wall_name, $boulder_name, $created, $count_finished, $average_rating, $average_difficulty, $max_rating, $max_difficulty) = $db->fetchRow())
{
    if($first_boulder && $last_wall == $wall_id){
        echo ",";
    }
    if($first_wall && $last_wall != $wall_id){
        echo "]},";
    }
    
    //Start
    if($last_wall != $wall_id){
        echo('{');
        echo('"wall_id":' . $wall_id .',');
        echo('"wall_name": "' . $wall_name .'",');
        echo('"boulders": [');
        
        $first_wall = true;
        $last_wall = $wall_id;
    }
    
    echo('{');
    
    echo('"boulder_id":' . $boulder_id .',');
    
    echo('"boulder_name": "' . $boulder_name .'",');
    echo('"created": "' . $created .'",');
    echo('"count_finished": ' . $count_finished .',');
    echo('"average_rating": ' . $average_rating .',');
    echo('"average_difficulty": ' . $average_difficulty .',');
    echo('"max_rating": ' . $max_rating .',');
    echo('"max_difficulty": ' . $max_difficulty.',');
    echo('"finished": ' . (in_array($boulder_id, $finished_boulders)?'1':'0'));
    echo('}');
    
    $first_boulder = true;
}
echo(']}]');
?>