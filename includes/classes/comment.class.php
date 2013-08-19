<?php
class Comment{
    public $Id = 0;
    public $Comment = "";
    public $Rating = 0;
    public $Difficulty = 0;
    public $UserId = 0;
    public $UserName = "";
    public $FacebookId = 0;
    
    function Comment($id,$comment,$rating,$difficulty,$userid,$username,$facebook_id){
        $this->Id = $id;
        $this->Comment = $comment;
        $this->Rating = $rating;
        $this->Difficulty = $difficulty;
        $this->UserId = $userid;
        $this->UserName = $username;
        $this->FacebookId = $facebook_id;
    }
}
?>