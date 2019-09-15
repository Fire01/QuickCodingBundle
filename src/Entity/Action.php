<?php 
namespace Fire01\QuickCodingBundle\Entity;

class Action {

    protected $type;
    protected $text;
    protected $path;
    protected $target;
    
    function __construct(){}
    
    function set(array $config){
        $this->type = isset($config['type']) && $config['type'] ? $config['type'] : null;
        $this->text = isset($config['text']) && $config['text'] ? $config['text'] : null;
        $this->path = isset($config['path']) && $config['path'] ? $config['path'] : null;
        $this->target = isset($config['target']) && $config['target'] ? $config['target'] : null;
    }
    
    function all(){
        return [
            'type'  => $this->type,
            'text'  => $this->text,
            'path'  => $this->path,
        ];
    }
    
    function getType(): string{
        return $this->type;
    }
    
    function setType(string $type){
        $this->type = $type;
    }
    
    function getText(): string{
        return $this->text;
    }
    
    function setText(string $text){
        $this->text = $text;
    }
    
    function getPath(): string{
        return $this->path;
    }
    
    function setPath($path){
        $this->path = $path;
    }
    
    function getTarget(): string{
        return $this->target;
    }
    
    function setTarget($target){
        $this->target = $target;
    }
}