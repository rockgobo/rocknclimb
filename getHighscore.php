<?php
include_once('config.php');

$user_id_get = $_GET['user_id']?$_GET['user_id']:false;

$scores = array();
$db = new DB();
$db->verboseMode = false;
$db->query('SELECT boulder_id, avg(difficulty) FROM boulders_comments WHERE difficulty > 0 group by boulder_id');
while(list($boulder_id,$score) = $db->fetchRow())
{
    $scores[$boulder_id] = $score;
}


//$db->query('SELECT id, username, facebook_id, count(bc.boulderid) AS finished FROM users u
//LEFT JOIN boulders_completed bc ON u.id = bc.userid WHERE id != 0 GROUP BY u.id ORDER BY finished DESC');
$user_scores = array();
$user_scores_real = array();
$user_finished_real = array();
$db->query('SELECT id, bc.boulderid FROM users u
            JOIN boulders_completed bc ON u.id = bc.userid WHERE id != 0');
 
while(list($user_id, $boulder_id) = $db->fetchRow())
{
    if($scores[$boulder_id]){
        $user_scores_real[$user_id] += $scores[$boulder_id];
        $user_finished_real[$user_id] += 1;
    }
    $user_scores[$user_id] += $scores[$boulder_id]?$scores[$boulder_id]:1;
}

if($user_id_get){
    $db->query('SELECT id, username, facebook_id, count(bc.boulderid) AS finished FROM users u
            LEFT JOIN boulders_completed bc ON u.id = bc.userid WHERE id != 0 AND (private = 0 OR u.id = '.$user_id_get.') GROUP BY u.id ORDER BY finished DESC');
}
else{
    $db->query('SELECT id, username, facebook_id, count(bc.boulderid) AS finished FROM users u
            LEFT JOIN boulders_completed bc ON u.id = bc.userid WHERE id != 0 AND private = 0 GROUP BY u.id ORDER BY finished DESC');
}

$first = false;
$highscores = array();

while(list($user_id, $user_name, $facebook_id, $finished) = $db->fetchRow())
{
    $highscore = new Highscore();
    $highscore->user_id = $user_id;
    $highscore->user_name = $user_name;
    $highscore->facebook_id = ($facebook_id?$facebook_id:'0');
    $highscore->finished =  $finished;
    $highscore->score = ($user_scores[$user_id]?round($user_scores[$user_id],2):0);
    
    if($user_finished_real[$user_id] > 0){
        $highscore->diff_average = round($user_scores_real[$user_id]/$user_finished_real[$user_id],2); 
    }else{;
          $highscore->diff_average =  0;  
    }
    array_push($highscores, $highscore);
}

usort($highscores,cmpScores);
$rank = 1;
foreach($highscores as $h){
    $h->rank = $rank++;
}

echo('{"highscores" : ' .json_encode($highscores)  .'}');

function cmpScores($highscore1, $highscore2)
{
    if ($highscore1->score == $highscore2->score) {
        return 0;
    }
    return ($highscore1->score > $highscore2->score) ? -1 : 1;
}
?>