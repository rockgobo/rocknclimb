<?php
class Boulder
{
    public $Id;
    public $Name;
    public $Description;
    public $WallId;
    public $UserId;
    public $UserName;
    public $Completed = false;
    public $StepGrip = false;
    public $StepSpax = true;
    public $Deleted = false;
    public $Comments = array();
    public $Created;
    public $IsRemix = false;
    public $Date = "";
    public $Diff = 1;
    
    public $LoadNodes = true;
    public $Nodes = array();
    
    public function Boulder($id = 0)
    {
        $this->Id = $id;
    }
    
    public function save(){
        
        $db = new DB();
        $db->verboseMode = false;
        $db->table = "boulders";
    
        //Set created date for new boulder
        if(!$this->Id){
            $this->Created = date('Y-m-d H:i:s');
            
            $db->setParams(array(
                'id' => $this->Id,
                'name' => $this->Name,
                'description' => $this->Description,
                'wall_id' => $this->WallId,
                'user_id' => $this->UserId,
                'step_grip' => $this->StepGrip?1:0,
                'step_spax' => $this->StepSpax?1:0,
                'isRemix' => $this->IsRemix?1:0,
                'created' => $this->Created));
        }
        else{
            
            $db->setParams(array(
                'id' => $this->Id,
                'name' => $this->Name,
                'description' => $this->Description,
                'wall_id' => $this->WallId,
                'user_id' => $this->UserId,
                'step_grip' => $this->StepGrip?1:0,
                'step_spax' => $this->StepSpax?1:0,
                'isRemix' => $this->IsRemix?1:0));
        }
    
        $db->save();
        
        if($this->Id == 0){
            $this->Id = $db->getInsertId();
        }
    }
    
    public function load($current_user_id = false){
        if($this->Id){
            $db = new DB();
            $db->verboseMode = false;
            $db->table = "boulders";
            $db->setParams(array(
                'id',
                'name',
                'description',
                'wall_id',
                'user_id',
                'step_grip',
                'step_spax',
                'deleted',
                'created',
                'isRemix'));
            $db->addFilter('id',$this->Id);
            $db->select();
            
            if(list($id,$name,$description,$wall_id,$user_id,$step_grip, $step_spax, $deleted,$created, $isRemix) = $db->fetchRow())
            {
                $this->Id = $id;
                $this->Name = $name;
                $this->Description = $description;
                $this->WallId = $wall_id;
                $this->UserId = $user_id;
                $this->StepGrip = $step_grip;
                $this->StepSpax = $step_spax;
                $this->Deleted = $deleted;
                $this->Created = $created;
                $this->IsRemix = $isRemix;
            }
            
            if($this->UserId){
                $db->clearQuery();
                $db->table = "users";
                $db->setParams(array(
                    'username'));
                $db->addFilter('id',$this->UserId);
                $db->select();
            
                if(list($username) = $db->fetchRow())
                {
                    $this->UserName = $username;
                }
            }
            
            if($current_user_id){
                $db->clearQuery();
                $db->table = "boulders_completed";
                $db->setParams(array('userid'));
                $db->addFilter('userid', $current_user_id);
                $db->addFilter('boulderid',$this->Id);
                $db->select();
                
                if(list($uid) = $db->fetchRow())
                {
                    $this->Completed = true;
                }
            }
            
            //load comments and calculate difficulty   
            $_difficulty = 0;
            $_count = 0;
            
            $this->Comments = array();
            
            $db->clearQuery();
            $db->verboseMode = false;
            $db->table = "boulders_comments bc, users u";
            $db->setParams(array("bc.comment","bc.rating","bc.difficulty","bc.user_id", "u.username", "u.facebook_id", "bc.id"));
            $db->setFilter("boulder_id =". $this->Id . " AND bc.user_id = u.id");
            $db->select();
            while(list($comment,$rating,$difficulty,$user_id,$user_name,$facebook_id,$cid) = $db->fetchRow()){
                $_difficulty += $difficulty;
                $_count += 1;
                array_push($this->Comments, new Comment($cid, $comment, $rating, $difficulty, $user_id, $user_name, $facebook_id));
            }
            if($_count){
                $this->Diff = round($_difficulty/$_count,2);
            }
            
            
            if($this->LoadNodes){
                $node_ids = array();
                $db->clearQuery();
                $db->verboseMode = false;
                $db->table = "boulder_nodes bn, nodes n";
                $db->setParams(array("id"));
                $db->setFilter("bn.boulder_id =". $this->Id. " AND bn.node_id = n.id");
                
                //$db->table = "nodes n";
                //$db->setParams(array("id"));
                //$db->addFilter("bn.boulder_id", $this->Id);
                
                $db->select();
                while(list($id) = $db->fetchRow()){
                    array_push($node_ids, $id);
                }
                
                foreach($node_ids as $nid){
                    $node = new Node($nid);
                    $node->load();
                    array_push($this->Nodes,$node);
                }
            }
        }
    }
      
    public function getJSON()
    {
        return '{ "Id": '.$this->Id.', "Name": "'.$this->Name.'", "Description": "'.$this->Description.'", "WallId": "'.$this->WallId.'", "UserId": "'.$this->UserId.'", "UserName": "'.$this->UserName.'", "StepGrip": "'.$this->UserName.'", "StepSpax": "'.$this->StepSpax.'", "Deleted": '.$this->Deleted.' }';
    }
}
?>