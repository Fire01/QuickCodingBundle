<?php 

namespace Fire01\QuickCodingBundle\Entity;

class Config {

    protected $title;
    protected $entity;
    protected $form;
    protected $formOptions;
    protected $viewType;
    protected $view = null;
    protected $path = ['view' => null, 'form' => null, 'remove' => null];
    protected $params;
    protected $action;
    protected $actionbar = [];
    protected $template = ['view' => null, 'form' => null];
    protected $ACL = null;
    
    function __construct(array $config=[]){
        $this->set($config);
    }
    
    function set(array $config){
        $this->setTitle(isset($config['title']) && $config['title'] ? $config['title'] : null);
        $this->setEntity(isset($config['entity']) && $config['entity'] ? $config['entity'] : null);
        $this->setForm(isset($config['form']) && $config['form'] ? $config['form'] : null);
        $this->setFormOptions(isset($config['formOptions']) && count($config['formOptions']) ? $config['formOptions'] : []);
        $this->setViewType(isset($config['viewType']) && $config['viewType'] ? $config['viewType'] : null);
        $this->setTemplate(isset($config['template']) && $config['template'] ? $config['template'] : null);
        
        $this->setParams(isset($config['params']) && $config['params'] ? $config['params'] : null);
        $this->setPath(isset($config['path']) && $config['path'] ? $config['path'] : null);
        $this->setAction(isset($config['action']) && $config['action'] ? $config['action'] : null);
        
        $this->setView(new View(isset($config['view']) ? $config['view'] : null));
        $this->setACL(new ACL(isset($config['ACL']) ? $config['ACL'] : null));
        
        return $this;
    }
    
    function all(){
        return [
            'title'     => $this->getTitle(),
            'entity'    => $this->getEntity(),
            'form'      => $this->getForm(),
            'viewType'  => $this->getViewType(),
            'template'  => $this->getTemplate(),
            'path'      => $this->getPath(),
            'params'      => $this->getParams(),
            'action'    => $this->getAction(),
            'actionbar' => $this->getActionbar(),
            'ACL'       => $this->getACL()
        ];
    }
    
    function getTitle(){
        return $this->title;
    }
    
    function setTitle(?string $title){
        $this->title = $title;
        
        return $this;
    }
    
    function getEntity(){
        return $this->entity;
    }
    
    function setEntity($entity=null){
        $this->entity = $entity;
        if(!$this->title) $this->title = $this->readable(substr(strrchr($this->entity, '\\'), 1));
        
        return $this;
    }
    
    function getForm(){
        return $this->form;
    }
    
    function setForm($form=null){
        $this->form = $form;
        
        return $this;
    }

    function getFormOptions(){
        return $this->formOptions;
    }
    
    function setFormOptions($options=[]){
        $this->formOptions = $options;
        
        return $this;
    }
    
    function getViewType(){
        return $this->viewType;
    }
    
    function setViewType(?string $viewType){
        $this->viewType = $viewType ? $viewType : 'DataTables';
        
        return $this;
    }
    
    function getView(): View{
        return $this->view ? $this->view : null;
    }
    
    function setView(?View $view){
        $this->view = $view;
        
        return $this;
    }
    
    function getParams(): array{
        return $this->params ? $this->params : [];
    }
    
    function setParams(?array $params){
        $this->params = $params;
        
        return $this;
    }
    
    function getPath(): array{
        return $this->path;
    }
    
    function setPath($path=null){
        $this->setPathView(is_array($path) ? (isset($path['view']) ? $path['view'] : null) : $path);
        $this->setPathForm(is_array($path) ? (isset($path['form']) ? $path['form'] : null) : $path);
        $this->setPathRemove(is_array($path) ? (isset($path['remove']) ? $path['remove'] : null) : $path);
        
        return $this;
    }
    
    function getPathView(){
        return $this->path['view'];
    }
    
    function setPathView($path){
        $this->path['view'] = $path;
        
        return $this;
    }
    
    function getPathForm(){
        return $this->path['form'];
    }
    
    function setPathForm($path){
        $this->path['form'] = $path;
        
        return $this;
    }
    
    function getPathRemove(){
        return $this->path['remove'];
    }
    
    function setPathRemove($path){
        $this->path['remove'] = $path;
        
        return $this;
    }
    
    function getAction(){
        return $this->action;
    }
    
    function setAction(?string $action){
        $this->action = $action;
        
        return $this;
    }
    
    function getActionbar(): array{
        return $this->actionbar;
    }
    
    function addActionbar(Action $action, $first=false){        
        if($action->getId() == '{auto}'){
            $action->setId($action->getType() . '_' . substr(md5(uniqid(mt_rand(), true)), 0, 6));
        }
        
        foreach ($this->getActionbar() as $item){
            if($action->getId() == $item->getId()){throw new \Exception(sprintf('Action with id "%s" already exist!', $item->getId()));}
        }
       
        if($first){
            array_unshift($this->actionbar, $action);
        }else{
            $this->actionbar[] = $action;
        }
        
        return $this;
    }
    
    function clearActionbar(){
        $this->actionbar = [];
        
        return $this;
    }
    
    function addActionbarFormSave($text=null, $target=null){
        $action = new Action();
        $action->setText($text ? $text : 'Save');
        $action->setType('submit');
        $action->setTarget($target);
        $action->setIcon('check');
        $action->setClick("$('form" . ($action->getTarget() ? "[name=\"form_" . $action->getTarget() . "\"]" : "") . "').find('button[type=submit]').click()");
        
        $this->addActionbar($action);
        
        return $this;
    }
    
    function addActionbarFormClose($text=null, $path=null){
        $action = new Action();
        $action->setText($text ? $text : 'Close');
        $action->setType('link');
        $action->setClass('uk-button-danger');
        $action->setPath($path ? $path : $this->getPathView());
        $action->setParams($this->getParams());
        $action->setTarget('route');
        $action->setIcon('close');
        
        $this->addActionbar($action);
        
        return $this;
    }
    
    function getTemplate(): array{
        return $this->template;
    }
    
    function setTemplate($template=null){
        $this->setTemplateView(is_array($template) ? (isset($template['view']) ? $template['view'] : null) : $template);
        $this->setTemplateForm(is_array($template) ? (isset($template['form']) ? $template['form'] : null) : $template);
        
        return $this;
    }
    
    function getTemplateView(){
        return $this->template['view'];
    }
    
    function setTemplateView($template){
        $this->template['view'] = $template ? $template : ($this->getViewType() == 'DataTables' ? '@quick_coding.view/component/dataTables.html.twig' : '@quick_coding.view/component/view.html.twig');
        
        return $this;
    }
    
    function getTemplateForm(){
        return $this->template['form'];
    }
    
    function setTemplateForm($template){
        $this->template['form'] = $template ? $template : '@quick_coding.view/component/form.html.twig';
        
        return $this;
    }
    
    function getACL(): ACL{
        return $this->ACL;
    }
    
    function setACL(?ACL $acl){
        $this->ACL = $acl;
        
        return $this;
    }
    
    function readable($string){
        return ucfirst(preg_replace(['/(?<=[^A-Z])([A-Z])/', '/(?<=[^0-9])([0-9])/'], ' $0', $string));
    }
}