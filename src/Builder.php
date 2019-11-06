<?php 
namespace Fire01\QuickCodingBundle;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Fire01\QuickCodingBundle\Entity\Config;
use Fire01\QuickCodingBundle\Entity\Action;
use Fire01\QuickCodingBundle\Services\Validator;
use Fire01\QuickCodingBundle\Event\BuilderFormEvent;
use Fire01\QuickCodingBundle\Event\BuilderRemoveEvent;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class Builder extends AbstractController {
     
    private $eventDispatcher;
    private $config;
    private $query;
    
    function __construct(array $config=[], Query $query, EventDispatcherInterface $eventDispatcher){
        $this->config = new Config();
        $this->query = $query;
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
            case 'export':
                return $this->export();
                break;
        }
        
        if(count($this->config->getACL()->getRead())) $this->denyAccessUnlessGranted($this->config->getACL()->getRead());
        
        return $this->generateView(true);
    }
    
    function generateView($create=false){
        Validator::view($this->config);
       
        $this->config->addActionbar(new Action([
            'type' => 'link',
            'class' => 'uk-button-danger',
            'text' => 'Close',
            'icon' => 'close',
            'path' => $this->getParameter('quick_coding.app_home'),
            'target' => 'route'
        ]));
        
        if($create){
            if($this->config->getACL()->canCreate($this->getUser()->getRoles())){
                $this->config->addActionbar(new Action([
                    'type' => 'link',
                    'text' => 'Create ' . $this->config->getTitle(),
                    'icon' => 'plus-circle',
                    'path' => $this->config->getPathForm(),
                    'params' => array_merge($this->config->getParams(), ['action' => 'create']),
                    'target' => 'route'
                ]));
            }
        }
        
        if($this->config->getView()->hasExport()){
            $this->config->addActionbar(new Action([
                'type' => 'button',
                'text' => 'Export',
                'icon' => 'download',
                'click' => "document.location = document.URL + '/export?' + encodeURI(getParamExport())"
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
                    'params' => array_merge($this->config->getParams(), ['action' => 'update', 'id' => $item->getId()]),
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
            
            return $this->redirectToRoute($this->config->getPathView(), $this->getConfig()->getParams());
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

            $DataTablesJSON = $this->query->set($this->config->getView())->generate();
            
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
    
    function export()
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $query = $request->query->all();
        
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
            ->setLength(0)
        ;

        $items = $this->query->set($this->config->getView())->generate();
        
        $array = [array_keys($this->config->getView()->getExport())];
        $array = array_merge($array, $items);
        
        $spreadsheet = new Spreadsheet();
        
        $sheet = $spreadsheet->getActiveSheet()->fromArray($array, null, 'A1');
        
        $sheet->setTitle($this->config->getTitle());
        $writer = new Xlsx($spreadsheet);
        
        $fileName = $this->config->getTitle() . ' - ' . date("Ymdhi"). '.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);
        
        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}