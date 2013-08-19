<?php
include_once('config.php');

$boulder_id = $_GET['boulder_id'];

if($boulder_id){
    $db = new DB();
    $db->query('SELECT id, facebook_id FROM users u
    JOIN boulders_completed bc ON u.id = bc.userid WHERE u.private = 0 && bc.boulderid='.$boulder_id.' order by bc.date DESC');

    $first = false; 
    echo('[');    
    while(list($user_id, $facebook_id) = $db->fetchRow())
    {
        if($first){
            echo ",";
        }
    
        echo('{');
        echo('"user_id":' . $user_id .',');
        echo('"facebook_id": ' . ($facebook_id?$facebook_id:'0'));
        echo('}');
    
        $first = true;
    }
    echo(']');
}
?>