<?php 
namespace Fire01\QuickCodingBundle\Services;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Fire01\QuickCodingBundle\Entity\Config;

class Builder extends AbstractController {
    
    protected $config;
    
    function __construct(array $config=[]){
        $this->config = new Config();
    }
    
    function setConfig(array $config){
        $this->config->set($config);
    }

    function build(){
        $request = $this->get('request_stack')->getCurrentRequest();
        $action = strtolower($request->attributes->get('_route_params')['action']);
        $id = $request->attributes->get('id');
        
        $this->config->setPath($request->attributes->get('_route'));
       
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
    
    function generateView(){
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
        $request = $this->get('request_stack')->getCurrentRequest();
        $repository = $this->getDoctrine()->getRepository($this->config->getEntity());
        $entity = $this->config->getEntity();
        
        $item = $id ? $repository->find($id) : new $entity();
        $form = $this->createForm($this->config->getForm(), $item, ['disabled' => !$edit]);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();
            
            return $this->redirectToRoute($this->config->getPath());
        }
        
        return $this->render("@quick_coding.view/component/form.html.twig", ["config" => $this->config, "form" => $form->createView()]);
    }
    
    function removeData($id){
        $item = $this->getDoctrine()->getRepository($this->config->getEntity())->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($item);
        $em->flush();
        
        if($this->get('request_stack')->getCurrentRequest()->isXmlHttpRequest()){
            return $this->json(['success' => true], JsonResponse::HTTP_OK);
        }else{
            return $this->redirectToRoute($this->config->getPath());
        }
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
            ], JsonResponse::HTTP_OK);
        }
        
        return $this->render('@quick_coding.view/component/dataTables.html.twig', ['config' => $this->config]);
    }
    
    /* @TODO: Move to new class, like repository or something*/
    function getDataDataTables($orders, $length, $start, $search){
        $repository = $this->getDoctrine()->getRepository($this->config->getEntity());
        
        $data = $repository->createQueryBuilder('t');
        $total = $repository->createQueryBuilder('t')->select('count(t.id)');
        
        if($search){
            foreach($this->config->getColumn() as $column){
                $data->orWhere('t.' . $column['name'] . ' LIKE :' . $column['name'])->setParameter( $column['name'], '%' . $search . '%');
                $total->orWhere('t.' . $column['name'] . ' LIKE :' . $column['name'])->setParameter( $column['name'], '%' . $search . '%');
            }
        }
        
        $total = $total->getQuery()->getSingleScalarResult();
        
        foreach($orders as $order){
            $data->orderBy('t.' . $this->config->getColumn()[$order['column']]['name'], $order['dir']);
        }
        
        $data = $data->setMaxResults($length)->setFirstResult($start)->getQuery()->getResult();
        
        return ['total' => $total, 'data' => $data];
    }
}