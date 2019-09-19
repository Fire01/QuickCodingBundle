<?php 
namespace Fire01\QuickCodingBundle\Entity;

class Action {

    protected $id;
    protected $type;
    protected $class;
    protected $text;
    protected $icon;
    
    protected $path;
    protected $params;
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
        $this->setParams(isset($config['params']) && $config['params'] ? $config['params'] : null);
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
            'params'=> $this->getParams(),
            'target'=> $this->getTarget(),
            'click' => $this->getClick(),
        ];
    }
    
    function getId(){
        return $this->id;
    }
    
    function setId(?string $id){
        $this->id = $id;
        
        return $this;
    }
    
    function getType(){
        return $this->type;
    }
    
    function setType(?string $type){
        $this->type = $type ? strtolower($type) : 'button';
        
        return $this;
    }
    
    function getClass(){
        return $this->class;
    }
    
    function setClass(?string $class){
        $this->class = $class;
        
        return $this;
    }
    
    function getText(){
        return $this->text;
    }
    
    function setText(?string $text){
        $this->text = $text;
        
        return $this;
    }
    
    function getIcon(){
        return $this->icon;
    }
    
    function setIcon(?string $icon){
        $this->icon = $icon;
        
        return $this;
    }
    
    function getPath(){
        return $this->path;
    }
    
    function setPath($path=null){
        $this->path = $path;
        
        return $this;
    }
    
    function getParams(): array{
        return $this->params ? $this->params : [];
    }
    
    function setParams($params=[]){
        $this->params = $params;
        
        return $this;
    }
    
    function getTarget(){
        return $this->target;
    }
    
    function setTarget($target=null){
        $this->target = $target;
        
        return $this;
    }
    
    function getClick(){
        return $this->click;
    }
    
    function setClick($click=null){
        $this->click = $click;
        
        return $this;
    }
}