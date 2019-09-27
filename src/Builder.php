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
    
    function __construct(array $config=[], EventDispatcherInterface $eventDispatcher){
        $this->config = new Config();
        $this->eventDispatcher = $eventDispatcher;
    }
    
    function setConfig(array $config){
        $this->config->set($config);
    }

    function build(){
        Validator::build($this->config);
        
        $request = $this->get('request_stack')->getCurrentRequest();
        $this->config->setPath($request->attributes->get('_route'));
        $action = strtolower($request->attributes->get('_route_params')['action']);
        $id = $request->attributes->get('id');
       
        switch($action){
            case 'create':
                return $this->generateForm();
                break;
            case 'read':
                return $this->generateForm($id, false);
                break;
            case 'update':
                return $this->generateForm($id, true);
                break;
            case 'delete':
                return $this->removeData($id);
                break;
        }
        
        return $this->generateView();
    }
    
    function generateView($pathNew=null){
        Validator::view($this->config);
        
        $this->config
            ->addActionbar(new Action([
                'type' => 'link',
                'class' => 'uk-button-danger',
                'text' => 'Close',
                'icon' => 'close',
                'path' => $this->getParameter('quick_coding.app_home'),
                'target' => 'route'
            ]))
            ->addActionbar(new Action([
                'type' => 'link',
                'text' => 'Create ' . $this->config->getTitle(),
                'icon' => 'plus-circle',
                'path' => $this->config->getPathForm(),
                'params' => ['action' => 'create'],
                'target' => 'route'
            ]))
        ;
        
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
            $this->config->addActionbar(new Action([
                'type' => 'link', 
                'text' => 'Edit', 
                'icon' => 'pencil',
                'path' => $this->config->getPathForm(),
                'params' => ['action' => 'update', 'id' => $item->getId()],
                'target' => 'route'
            ]));
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
        /*
        $request = $this->get('request_stack')->getCurrentRequest();
        
        if($request->query->get('order')){
            $order = explode('-', $request->query->get('order'));
        }else{
            $order = count($this->config->getColumn()) ? [$this->config->getColumn()[0]['name'], 'Asc'] : [];
        }
        
        $data =  $this->data($entityID, $order, $request->query->getInt('page', 1), $perPage, $request->query->get('search'));
        return $this->render('@quick_coding.view/component/view.html.twig', ['title' => $title, 'column' => $view, 'path' => $request->attributes->get('_route'), 'order' => $order, 'data' => $data]);
        */
    }
    
    function generateViewDataTables(){
        $request = $this->get('request_stack')->getCurrentRequest();
        $query = $request->query->all();
        if(isset($query['type']) && $query['type'] == 'json'){
            
            $serializer = new Serializer([new ObjectNormalizer()]);
            $column = array_map(function($item) {return $item['name'];}, $this->config->getColumn());
            array_unshift($column,'id');
            
            $DataTablesJSON = $this->getDataDataTables($query['order'], $query['length'], $query['start'], $query['search']['value']);
            return $this->json([
                'draw'              => $query['draw'],
                'recordsTotal'      => $DataTablesJSON['total'],
                'recordsFiltered'   => $DataTablesJSON['total'],
                'data'              => $serializer->normalize($DataTablesJSON['data'], null, ['attributes' => $column]),
                'error'             => null
            ]);
        }
        return $this->render($this->config->getTemplateView(), ['config' => $this->config]);
    }
    
    /* @TODO: Move to new class, like repository or something*/
    function getDataDataTables($orders, $length, $start, $search){
        $repository = $this->getDoctrine()->getRepository($this->config->getEntity());
        
        $data = $repository->createQueryBuilder('t');
        $total = $repository->createQueryBuilder('t')->select('count(t.id)');
        
        if($search){
            foreach($this->config->getColumn() as $column){
                $data->where('t.' . $column['name'] . ' LIKE :' . $column['name'])->setParameter( $column['name'], $search);
                $total->where('t.' . $column['name'] . ' LIKE :' . $column['name'])->setParameter( $column['name'], $search);
            }
        }
        
        $event = new BuilderViewEvent($data, $total);
        
        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch('quick_coding.builder_view_where', $event);
        }
        
        $total = $total->getQuery()->getSingleScalarResult();
        
        foreach($orders as $order){
            $data->orderBy('t.' . $this->config->getColumn()[$order['column']]['name'], $order['dir']);
        }
        
        $data = $data->setMaxResults($length)->setFirstResult($start)->getQuery()->getResult();
        
        return ['total' => $total, 'data' => $data];
    }
}