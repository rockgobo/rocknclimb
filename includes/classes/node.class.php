<?php
class Node
{
    public $id = 0;
    public $type;
    public $top;
    public $left;
    public $width;
    public $height;
    public $text;
    public $boulder;
    public $wall_id;
    
    public function Node($id = 0)
    {
        $this->id = $id;
    }
    
    public function load(){
        if($this->id){
            $db = new DB();
            $db->verboseMode = false;
            $db->table = "nodes";
            $db->setParams(array(
                'id',
                'top',
                'boulder_id',
                'nodes.left',
                'width',
                'height',
                'nodes.text',
                'nodes.type',
                'wall_id'));
            $db->addFilter('id',$this->id);
            $db->select();
            
            if(list($id,$top,$boulder_id,$left,$width,$height, $text, $type, $wall_id) = $db->fetchRow())
            {
                $this->id = $id;
                $this->top = $top;
                $this->boulder = $boulder_id;
                $this->left = $left;
                $this->width = $width;
                $this->height = $height;
                $this->text = $text;
                $this->type = $type;
                $this->wall_id = $wall_id;
            }
        }
    }
    
    public function save(){
        $db = new DB();
        $db->verboseMode = false;
        $db->table = "nodes";
        $db->setParams(array(
            'id'=>$this->id,
            'top'=>$this->top,
            'boulder_id'=> $this->boulder,
            'nodes.left'=>$this->left,
            'width'=>$this->width,
            'height'=>$this->height,
            'nodes.text'=>$this->text,
            'nodes.type'=>$this->type,
            'wall_id'=>$this->wall_id));
        $db->save();
        
        if($this->id == 0){
            $this->id = $db->getInsertId();
        }
    }
    
    
    
    public function getJSON()
    {
        return '{ "top": '.$this->top.', "left": '.$this->left.', "width": '.$this->width.', "height": '.$this->height.', "text": "'.$this->text.'", "id": "'.$this->id.'", "editable": true, "type": "'.$this->type.'", "wall_id": "'.$this->wall_id.'" }';
    }
}
?>