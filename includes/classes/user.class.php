<?php
class User
{
    public $id = 0;
    public $username;
    public $facebook_name;
    public $facebook_username;
    public $facebook_id = 0;
    public $email;
    public $password;
    public $finishedBoulders = array();
    public $private = 0;
    public $finished1 = 0;
    public $finished2 = 0;
    public $finished3 = 0;
    public $finished4 = 0;
    public $finished5 = 0;
    
    public function User($id = 0)
    {
        $this->id = $id;
    }
    
    public function save(){
        $db = new DB();
        $db->verboseMode = false;
        $db->table = "users";
        $db->setParams(array(
            'id'=>$this->id,
            'username'=>$this->username,
            'facebook_name'=>$this->facebook_name,
            'facebook_username'=>$this->facebook_username,
            'facebook_id'=>$this->facebook_id,
            'email'=>$this->email,
            'password'=>$this->password,
            'private'=>$this->private));
        $db->save();

        if($this->id == 0){
            $this->id = $db->getInsertId();
        }
    }
    
    public function checkUsername(){
        $db = new DB();
        $db->verboseMode = false;
        $db->table = "users";
        $db->setParams(array(
            'id',
            'username'
            ));
        $db->addFilter("username",$this->username);
        $db->select();
        
        if(list($id,$username) = $db->fetchRow()){
            return false;
        }
        return true;
    }
    
    public function load(){
        //if($this->id || $this->username || $this->facebook_name){
            $db = new DB();
            $db->verboseMode = false;
            $db->table = "users";
            $db->setParams(array(
                'id',
                'username',
                'facebook_name',
                'facebook_username',
                'facebook_id',
                'email',
                'password',
                'private'));
            if($this->id){
                $db->addFilter('id',$this->id);
            }
            else if($this->facebook_name){
                $db->addFilter('facebook_name',$this->facebook_name);
            }
            else if($this->username){
                $db->addFilter('username',$this->username);
            }
            else{
                return;
            }
            
            $db->select();
            
            if(list($id,$username,$facebook_name,$facebook_username,$facebook_id,$email,$password,$private) = $db->fetchRow())
            {
                $this->id = $id;
                $this->username = $username;
                $this->facebook_name = $facebook_name;
                $this->facebook_username = $facebook_username;
                $this->facebook_id = $facebook_id;
                $this->email = $email;
                $this->password = $password;
                $this->private = $private;
            }
            
            //load finished boulders
            if($this->id){
                $boulderids = array();
                
                $db->clearQuery();
                $db->verboseMode = false;
                $db->table = "boulders_completed";
                $db->setParams(array("boulderid, date"));
                $db->setOrderBy("date DESC");
                $db->addFilter("userid",$this->id);
                $db->select();
                while(list($boulderid, $date) = $db->fetchRow()){
                    $boulder = new Boulder($boulderid);
                    $boulder->LoadNodes = false;
                    $boulder->load($this->id);
                    $boulder->Date = date( 'd.m.Y', strtotime($date));
                    array_push($this->finishedBoulders, $boulder);
                    
                    if(round($boulder->Diff)==1){
                        $this->finished1 += 1;
                    }
                    if(round($boulder->Diff)==2){
                        $this->finished2 += 1;
                    }
                    if(round($boulder->Diff)==3){
                        $this->finished3 += 1;
                    }
                    if(round($boulder->Diff)==4){
                        $this->finished4 += 1;
                    }
                    if(round($boulder->Diff)== 5){
                        $this->finished5 += 1;
                    }
                }
            }
        //}
    }
    
    public function isValid($password){
        if($this->facebook_id){
            return true;
        }
        return strcmp( $this->password, $password ) == 0;
    }
    
    public function getJSON()
    {
        return '[ "id": '.$this->id.', "username": "'.$this->username.'", "facebook_name": "'.$this->facebook_name.'"]';
    }
}
?>