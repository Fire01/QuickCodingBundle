<?php 
namespace Fire01\QuickCodingBundle\Entity;

class Config {

    protected $title;
    protected $entity;
    protected $form;
    protected $viewType;
    protected $column;
    protected $path;
    protected $action;
    protected $actionbar = [];
    
    function __construct(array $config=[]){
        $this->set($config);
    }
    
    function set(array $config){
        $this->title = isset($config['title']) && $config['title'] ? $config['title'] : null;
        $this->entity = isset($config['entity']) && $config['entity'] ? $config['entity'] : null;
        $this->form = isset($config['form']) && $config['form'] ? $config['form'] : null;
        $this->column = isset($config['column']) && $config['column'] ? $config['column'] : [];
        $this->path = isset($config['path']) && $config['path'] ? $config['path'] : null;
        $this->action = isset($config['action']) && $config['action'] ? $config['action'] : null;
        $this->actionbar = isset($config['actionbar']) && $config['actionbar'] ? $config['actionbar'] : [];
    }
    
    function all(){
        return [
            'title'     => $this->getTitle(),
            'entity'    => $this->getEntity(),
            'form'      => $this->getForm(),
            'viewType'  => $this->getViewType(),
            'column'    => $this->getColumn(),
            'path'      => $this->getPath(),
            'action'    => $this->getAction(),
        ];
    }
    
    function getTitle(): string{
        $title = $this->title;
        if(!$this->title) $title = $this->readable(substr(strrchr($this->entity, '\\'), 1));
        
        return $this->readable($this->action . $title);
    }
    
    function setTitle(string $title){
        $this->title = $title;
    }
    
    function getEntity(){
        return $this->entity;
    }
    
    function setEntity($entity){
        $this->entity = $entity;
    }
    
    function getForm(){
        return $this->form;
    }
    
    function setForm($form){
        $this->form = $form;
    }
    
    function getViewType(){
        return $this->viewType ? $this->viewType : 'DataTables';
    }
    
    function setViewType(string $viewType){
        $this->viewType = $viewType;
    }
    
    function getColumn(): array{
        return $this->column ? $this->column : [];
    }
    
    function setColumn(array $column){
        $this->column = $column;
    }
    
    function getPath(){
        return $this->path;
    }
    
    function setPath(string $path){
        $this->path = $path;
    }
    
    function getAction(): string{
        return $this->action;
    }
    
    function setAction(string $action){
        $this->action = $action;
    }
    
    function getActionbar(): array{
        return $this->actionbar;
    }
    
    function addActionbar(Action $action){
        $this->actionbar[] = $action;
    }
    
    function clearActionbar(){
        $this->actionbar = [];
    }
    
    function addActionbarSave($text='Save', $target=null){
        $action = new Action();
        $action->setText($text);
        $action->setType('submit');
        $action->setTarget($target);
        
        $this->addActionbar($action);
    }
    
    function addActionbarClose($text='Close', $path=null){
        $action = new Action();
        $action->setText($text);
        $action->setType('close');
        $action->setPath($path ? $path : $this->path);
        
        $this->addActionbar($action);
    }
    
    function readable($string){
        return ucfirst(preg_replace(['/(?<=[^A-Z])([A-Z])/', '/(?<=[^0-9])([0-9])/'], ' $0', $string));
    }
   
}