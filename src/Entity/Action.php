<?php 
namespace Fire01\QuickCodingBundle\Entity;

class Action {

    protected $id;
    protected $type;
    protected $class;
    protected $text;
    protected $icon;
    
    protected $path;
    protected $target;
    protected $click;
    
    function __construct(array $config=[]){
        $this->set($config);
    }
    
    function set(array $config){
        $this->setId(isset($config['id']) && $config['id'] ? $config['id'] : '{auto}');
        $this->setType(isset($config['type']) && $config['type'] ? $config['type'] : null);
        $this->setClass(isset($config['class']) && $config['class'] ? $config['class'] : null);
        $this->setText(isset($config['text']) && $config['text'] ? $config['text'] : null);
        $this->setIcon(isset($config['icon']) && $config['icon'] ? $config['icon'] : null);
        
        $this->setPath(isset($config['path']) && $config['path'] ? $config['path'] : null);
        $this->setTarget(isset($config['target']) && $config['target'] ? $config['target'] : null);
        $this->setClick(isset($config['click']) && $config['click'] ? $config['click'] : null);
    }
    
    function all(){
        return [
            'id'    => $this->getId(),
            'type'  => $this->getType(),
            'class' => $this->getClass(),
            'text'  => $this->getText(),
            'icon'  => $this->getIcon(),
            'path'  => $this->getPath(),
            'target'=> $this->getTarget(),
            'click' => $this->getClick(),
        ];
    }
    
    function getId(){
        return $this->id;
    }
    
    function setId(?string $id){
        $this->id = $id;
    }
    
    function getType(){
        return $this->type;
    }
    
    function setType(?string $type){
        $this->type = $type ? strtolower($type) : 'button';
    }
    
    function getClass(){
        return $this->class;
    }
    
    function setClass(?string $class){
        $this->class = $class;
    }
    
    function getText(){
        return $this->text;
    }
    
    function setText(?string $text){
        $this->text = $text;
    }
    
    function getIcon(){
        return $this->icon;
    }
    
    function setIcon(?string $icon){
        $this->icon = $icon;
    }
    
    function getPath(){
        return $this->path;
    }
    
    function setPath($path=null){
        $this->path = $path;
    }
    
    function getTarget(){
        return $this->target;
    }
    
    function setTarget($target=null){
        $this->target = $target;
    }
    
    function getClick(){
        return $this->click;
    }
    
    function setClick($click=null){
        $this->click = $click;
    }
}