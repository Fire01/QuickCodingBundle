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
        $this->setTitle(isset($config['title']) && $config['title'] ? $config['title'] : null);
        $this->setEntity(isset($config['entity']) && $config['entity'] ? $config['entity'] : null);
        $this->setForm(isset($config['form']) && $config['form'] ? $config['form'] : null);
        $this->setColumn(isset($config['column']) && $config['column'] ? $config['column'] : []);
        $this->setViewType(isset($config['viewType']) && $config['viewType'] ? $config['viewType'] : null);
        
        $this->setPath(isset($config['path']) && $config['path'] ? $config['path'] : null);
        $this->setAction(isset($config['action']) && $config['action'] ? $config['action'] : null);
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
            'actionbar' => $this->getActionbar(),
        ];
    }
    
    function getTitle(){
        return $this->title;
    }
    
    function setTitle(?string $title){
        $this->title = $title;
    }
    
    function getEntity(){
        return $this->entity;
    }
    
    function setEntity($entity=null){
        $this->entity = $entity;
        if(!$this->title) $this->title = $this->readable(substr(strrchr($this->entity, '\\'), 1));
    }
    
    function getForm(){
        return $this->form;
    }
    
    function setForm($form=null){
        $this->form = $form;
    }
    
    function getViewType(){
        return $this->viewType;
    }
    
    function setViewType(?string $viewType){
        $this->viewType = $viewType ? $viewType : 'DataTables';
    }
    
    function getColumn(): array{
        return $this->column ? $this->column : [];
    }
    
    function setColumn(?array $column){
        $this->column = $column;
    }
    
    function getPath(){
        return $this->path;
    }
    
    function setPath(?string $path){
        $this->path = $path;
    }
    
    function getAction(){
        return $this->action;
    }
    
    function setAction(?string $action){
        $this->action = $action;
    }
    
    function getActionbar(): array{
        return $this->actionbar;
    }
    
    function addActionbar(Action $action){        
        if($action->getId() == '{auto}'){
            $action->setId($action->getType() . '_' . substr(md5(uniqid(mt_rand(), true)), 0, 6));
        }
        
        foreach ($this->getActionbar() as $item){
            if($action->getId() == $item->getId()){throw new \Exception('Action with id "' . $item->getId() . '" already exist!');}
        }
       
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
        $action->setIcon('check');
        $action->setClick("$('form" . ($action->getTarget() ? "[name=\"form_" . $action->getTarget() . "\"]" : "") . "').find('> button[type=submit]').click()");
        
        $this->addActionbar($action);
    }
    
    function addActionbarClose($text='Close', $path=null){
        $action = new Action();
        $action->setText($text);
        $action->setType('link');
        $action->setClass('uk-button-danger');
        $action->setPath($path ? $path : $this->path);
        $action->setTarget('route');
        $action->setIcon('close');
        
        $this->addActionbar($action);
    }
    
    function readable($string){
        return ucfirst(preg_replace(['/(?<=[^A-Z])([A-Z])/', '/(?<=[^0-9])([0-9])/'], ' $0', $string));
    }
   
}