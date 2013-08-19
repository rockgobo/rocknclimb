<?php
define('_LOCAL',true);

if(_LOCAL){
    define('_MYSQL_DB_NAME','boulder');
    define('_MYSQL_HOST','localhost');
    define('_MYSQL_USERNAME','climber');
    define('_MYSQL_PASSWORD','climber');
    define('_FACEBOOK_APPID','369309729831528');
    define('_FACEBOOK_CHANNEL_URL','http://localhost/boulderrep/index.php');
}
else{
    define('_MYSQL_DB_NAME','DB1196928');
    define('_MYSQL_HOST','rdbms.strato.de');
    define('_MYSQL_USERNAME','U1196928');
    define('_MYSQL_PASSWORD','atwqnmgc79');
    define('_FACEBOOK_APPID','457919904268179');
    define('_FACEBOOK_CHANNEL_URL','http://rocknclimb.de/index.php');
}
include_once('includes/db.class.php');

include_once('includes/classes/wall.class.php');
include_once('includes/classes/boulder.class.php');
include_once('includes/classes/node.class.php');
include_once('includes/classes/user.class.php');
include_once('includes/classes/comment.class.php');
include_once('includes/classes/highscore.class.php');
?>