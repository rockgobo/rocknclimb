<?php
class Wall
{
    public $Id;
    public $Name;
    public $Image;
    public $Left;
    public $Right;
    
    public function Wall($id = 0)
    {
        $this->Id = $id;
    }
    
    public function save(){
        $db = new DB();
        $db->verboseMode = false;
        $db->table = "walls";
        $db->setParams(array(
            'id'=>$this->Id,
            'name'=>$this->Name,
            'image'=>$this->Image));
        $db->save();
    }
    
    public function load(){
        if($this->Id){
            $db = new DB();
            $db->verboseMode = false;
            $db->table = "walls";
            $db->setParams(array(
                'id',
                'name',
                'image',
                'left_wall',
                'right_wall'));
            $db->addFilter('id',$this->Id);
            $db->select();
            
            if(list($id,$name,$image,$left,$right) = $db->fetchRow())
            {
                $this->Id = $id;
                $this->Name = $name;
                $this->Image = $image;
                $this->Left = $left;
                $this->Right = $right;
            }
        }
    }
    
    public function getJSON()
    {
        return '{ "Id": '.$this->Id.', "Name": "'.$this->Name.'", "Image": "'.$this->Image.'" }';
    }
}
?>