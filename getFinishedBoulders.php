<?php
include_once('config.php');

$user_id = $_GET['user_id'];

if($user_id){
    //GET BOULDER AND DIFFICULTY
    $boulders = array(); 
    $db = new DB();
    $db->query('SELECT
    b.id as boulder_id,
    ifnull(avg(bct.difficulty),0) as average_diff
    FROM boulders b 
    LEFT OUTER JOIN boulders_comments bct on bct.boulder_id = b.id WHERE bct.difficulty <> 0
    GROUP BY b.id');
    while(list($boulder_id, $diff) = $db->fetchRow()){
        $boulders[$boulder_id] = $diff;
    }

    
    $db->query('SELECT b.id, b.wall_id, b.name, bc.date FROM boulders b
    JOIN boulders_completed bc ON b.id = bc.boulderid 
    WHERE bc.userid = '.$user_id.' ORDER BY date DESC');

    $first = false; 
    echo('[');    
    while(list($boulder_id, $wall_id, $name, $date) = $db->fetchRow())
    {
        if($first){
            echo ",";
        }
        
        echo('{');
        echo('"name":"' . $name .'",');
        echo('"id":' . $boulder_id .',');
        echo('"wall_id": ' . $wall_id.',');
        echo('"date": "' . date( 'd.m.Y', strtotime($date)).'",');
        echo('"difficulty":' . round($boulders[$boulder_id]?$boulders[$boulder_id]:0,2) );
        echo('}');
        
        $first = true;
    }
    echo(']');
}
?>