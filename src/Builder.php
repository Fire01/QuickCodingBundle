<?php 
namespace Fire01\QuickCodingBundle;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Fire01\QuickCodingBundle\Entity\Config;
use Fire01\QuickCodingBundle\Entity\Action;
use Fire01\QuickCodingBundle\Services\Validator;
use Fire01\QuickCodingBundle\Event\BuilderFormEvent;
use Fire01\QuickCodingBundle\Event\BuilderRemoveEvent;
use Fire01\QuickCodingBundle\Event\BuilderViewEvent;

class Builder extends AbstractController {
     
    private $eventDispatcher;
    private $config;
    private $rest;
    
    function __construct(array $config=[], Rest $rest, EventDispatcherInterface $eventDispatcher){
        $this->config = new Config();
        $this->rest = $rest;
        $this->eventDispatcher = $eventDispatcher;
    }
    
    function getConfig(){
        return $this->config;
    }
    
    function setConfig(array $config){
        $this->config->set($config);
        
        return $this;
    }

    function build(){
        Validator::build($this->config);
        
        $request = $this->get('request_stack')->getCurrentRequest();
        $this->config->setPath($request->attributes->get('_route'));
        $action = strtolower($request->attributes->get('_route_params')['action']);
        $id = $request->attributes->get('id');
        
        switch($action){
            case 'create':
                if(count($this->config->getACL()->getCreate())) $this->denyAccessUnlessGranted($this->config->getACL()->getCreate());
                
                return $this->generateForm();
                break;
            case 'read':
                if(count($this->config->getACL()->getRead())) $this->denyAccessUnlessGranted($this->config->getACL()->getRead());
                
                return $this->generateForm($id, false);
                break;
            case 'update':
                if(count($this->config->getACL()->getUpdate())) $this->denyAccessUnlessGranted($this->config->getACL()->getUpdate());
                
                return $this->generateForm($id, true);
                break;
            case 'delete':
                if(count($this->config->getACL()->getDelete())) $this->denyAccessUnlessGranted($this->config->getACL()->getDelete());
                
                return $this->removeData($id);
                break;
        }
        
        if(count($this->config->getACL()->getRead())) $this->denyAccessUnlessGranted($this->config->getACL()->getRead());
        
        return $this->generateView();
    }
    
    function generateView($pathNew=null, $params=null){
        Validator::view($this->config);
       
        $this->config->addActionbar(new Action([
            'type' => 'link',
            'class' => 'uk-button-danger',
            'text' => 'Close',
            'icon' => 'close',
            'path' => $this->getParameter('quick_coding.app_home'),
            'target' => 'route'
        ]));
        
        if($this->config->getACL()->canCreate($this->getUser()->getRoles())){
            $this->config->addActionbar(new Action([
                'type' => 'link',
                'text' => 'Create ' . $this->config->getTitle(),
                'icon' => 'plus-circle',
                'path' => $pathNew ? $pathNew : $this->config->getPathForm(),
                'params' => $params ? array_merge($params, ['action' => 'create']) : ['action' => 'create'],
                'target' => 'route'
            ]));
        }
        
        switch ($this->config->getViewType()){
            case 'Native':
                throw new \Exception('TODO: createViewNative');
                break;
            case 'DataTables':
                return $this->generateViewDataTables();
                break;
        }
        
        return;
    }
    
    function generateForm($id=null, $edit=true){
        Validator::form($this->config);
        
        $request = $this->get('request_stack')->getCurrentRequest();
        $repository = $this->getDoctrine()->getRepository($this->config->getEntity());
        $entity = $this->config->getEntity();
        
        $item = $id ? $repository->find($id) : new $entity();
        $form = $this->createForm($this->config->getForm(), $item, ['disabled' => !$edit]);
        
        $event = new BuilderFormEvent($item, $form);
        
        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch('quick_coding.builder_form_before_submit', $event);
        }
        
        $form->handleRequest($request);
        
        $this->config->addActionbarFormClose();
        if($edit){
            $this->config->addActionbarFormSave();
        }else{
            if($this->config->getACL()->canUpdate($this->getUser()->getRoles())){
                $this->config->addActionbar(new Action([
                    'type' => 'link',
                    'text' => 'Edit',
                    'icon' => 'pencil',
                    'path' => $this->config->getPathForm(),
                    'params' => ['action' => 'update', 'id' => $item->getId()],
                    'target' => 'route'
                ]));
            }
        }
        
        if($form->isSubmitted() && $form->isValid()) {

            $event = new BuilderFormEvent($item, $form);
            
            if ($this->eventDispatcher) {
                $this->eventDispatcher->dispatch('quick_coding.builder_form_before_save', $event);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();
            
            return $this->redirectToRoute($this->config->getPathView());
        }
        
        return $this->render($this->config->getTemplateForm(), ["config" => $this->config, "form" => $form->createView()]);
    }
    
    function removeData($id){
        $isAjax = $this->get('request_stack')->getCurrentRequest()->isXmlHttpRequest();
        Validator::remove($this->config, $isAjax);
        
        $item = $this->getDoctrine()->getRepository($this->config->getEntity())->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($item);
        
        $event = new BuilderRemoveEvent($item, $em);
        
        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch('quick_coding.builder_remove_after', $event);
        }
        
        $em->flush();
        
        if($isAjax){
            return $this->json(['success' => true]);
        }else{
            return $this->redirectToRoute($this->config->getPathView());
        }
    }
    
    function addListener($eventName, $listener, $priority = 0){
        return $this->eventDispatcher->addListener($eventName, $listener, $priority);
    }
    
    function generateViewNative(){
        throw new \Exception("@TODO");
        return;
    }
    
    function generateViewDataTables(){
        $request = $this->get('request_stack')->getCurrentRequest();
        $query = $request->query->all();
        if(isset($query['type']) && $query['type'] == 'json'){
            
            $page = $query['start'] / $query['length'];
            $page = $page < 0 ? 1 : $page + 1;
            $orders = [];
            $selects = array_flip($this->config->getView()->getSelect());
            $selectsKeys = array_keys($selects);
            foreach($query['order'] as $order){
                $key = $selects[$selectsKeys[$order['column']]];
                $orders[$key] = $order['dir'];
            }
            
            $this->config->getView()
                ->setEntity($this->config->getEntity())
                ->addSelect(['id' => 'id'])
                ->setOrders($orders)
                ->setQ($query['search']['value'])
                ->setLength($query['length'])
                ->setPage($page)
                ->setTotal(true)
            ;

            $DataTablesJSON = $this->rest->set($this->config->getView())->generate();
            
            return $this->json([
                'draw'              => $query['draw'],
                'recordsTotal'      => $DataTablesJSON['total'],
                'recordsFiltered'   => $DataTablesJSON['total'],
                'data'              => $DataTablesJSON['data'],
                'error'             => null
            ]);
        }
        return $this->render($this->config->getTemplateView(), ['config' => $this->config]);
    }
}