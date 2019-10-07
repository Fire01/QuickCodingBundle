<?php 
namespace Fire01\QuickCodingBundle\Entity;

class ACL {

    protected $create = [];
    protected $read = [];
    protected $update = [];
    protected $delete = [];
    
    function __construct($ACL){
        $this->set($ACL);
    }
    
    function set($ACL){
        $create = $ACL;
        $read = $ACL;
        $update = $ACL;
        $delete = $ACL;
        
        if(is_array($ACL)){
            if(isset(array_keys($ACL)[0]) && array_keys($ACL)[0]){
                $create = [];
                $read   = [];
                $update = [];
                $delete = [];
            }
            
            if(isset($ACL['create']))   $create = $ACL['create'];
            if(isset($ACL['read']))     $read   = $ACL['read'];
            if(isset($ACL['update']))   $update = $ACL['update'];
            if(isset($ACL['delete']))   $delete = $ACL['delete'];
        }
        
        $this->setCreate($create);
        $this->setRead($read);
        $this->setUpdate($update);
        $this->setDelete($delete);
        
        return $this;
    }
    
    function all(){
        return [
            'create' => $this->getCreate(),
            'read' => $this->getRead(),
            'update' => $this->update(),
            'delete' => $this->getDelete(),
        ];
    }
    
    function getCreate(){
        return $this->create;
    }
    
    function setCreate($roles){
        $this->create = array_filter(is_array($roles) ? $roles : [$roles]);
        
        return $this;
    }
    
    function canCreate($roles) : bool{
        if(count($this->getCreate())){
            return count(array_intersect($this->getCreate(), $roles)) > 0 ? true : false;
        }
        
        return true;
    }
    
    function getRead(){
        return $this->read;
    }
    
    function setRead($roles){
        $this->read = array_filter(is_array($roles) ? $roles : [$roles]);
        
        return $this;
    }
    
    function canRead($roles) : bool{
        if(count($this->getRead())){
            return count(array_intersect($this->getCreate(), $roles)) > 0 ? true : false;
        }
        
        return true;
    }
    
    function getUpdate(){
        return $this->update;
    }
    
    function setUpdate($roles){
        $this->update = array_filter(is_array($roles) ? $roles : [$roles]);
        
        return $this;
    }
    
    function canUpdate($roles) : bool{
        if(count($this->getUpdate())){
            return count(array_intersect($this->getCreate(), $roles)) > 0 ? true : false;
        }
        
        return true;
    }
    
    function getDelete(){
        return $this->delete;
    }
    
    function setDelete($roles){
        $this->delete = array_filter(is_array($roles) ? $roles : [$roles]);
        
        return $this;
    }
    
    function canDelete($roles) : bool{
        if(count($this->getDelete())){
            return count(array_intersect($this->getCreate(), $roles)) > 0 ? true : false;
        }
        
        return true;
    }
}