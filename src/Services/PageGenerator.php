<?php  
namespace Fire01\QuickCodingBundle\Services;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;

class PageGenerator extends AbstractController { 
    
    private $knpPaginator;
    
    function __construct(PaginatorInterface $knpPaginator){
        $this->knpPaginator = $knpPaginator;
    }
    
    function generate($configuration){
        $request = $this->get('request_stack')->getCurrentRequest();
        $action = strtolower($request->attributes->get('_route_params')['action']);
        $id = $request->attributes->get('id');
        
        switch($action){
            case 'create':
                return $this->generateForm($configuration['title'], $configuration['entity'], $configuration['form']);
                break;
            case 'read':
                return $this->generateForm($configuration['title'], $configuration['entity'], $configuration['form'], $id, false);
                break;
            case 'update':
                return $this->generateForm($configuration['title'], $configuration['entity'], $configuration['form'], $id, true);
                break;
            case 'delete':
                return $this->remove($configuration['entity'], $id, $request->attributes->get('_route'));
                break;
        }
        
        $type = isset($configuration['list']) && $configuration['list'] ? $configuration['list'] : 'datatables';
        if($type == 'datatables'){
            return $this->generateDataTables($configuration['title'], $configuration['entity'], $configuration['view']);
        }else{
            return $this->generateView($configuration['title'], $configuration['entity'], $configuration['view']);
        }
    }
    
    function generateView($title, $entityID, $view, $perPage=25){
        $request = $this->get('request_stack')->getCurrentRequest();
        
        $order = [];
        if($request->query->get('order')){
            $order = explode("-", $request->query->get('order'));
        }else{
            if(isset($view[0])){
                $order = [$view[0]['name'], 'Asc'];
            }
        }
        
        $data =  $this->data($entityID, $order, $request->query->getInt('page', 1), $perPage, $request->query->get('search'));
        return $this->render('@quick_coding.view/component/view.html.twig', ["title" => $title, "column" => $view, "path" => $request->attributes->get('_route'), "order" => $order, "data" => $data]);
    }
   
    function generateDataTables($title, $entityID, $view){
        $request = $this->get('request_stack')->getCurrentRequest();
        $path = $this->generateUrl($request->attributes->get('_route'), []);
        $query = $request->query->all();
        
        if(isset($query['type']) && $query['type'] == 'json'){
            $dataTables = $this->dataTable($entityID, $view, $query["order"], $query["length"], $query["start"], $query["search"]["value"]); 
            return $this->json([
                "draw"  => $query["draw"],
                "recordsTotal"  => $dataTables["total"],
                "recordsFiltered"  => $dataTables["total"],
                "data"  => $dataTables["data"],
                "error"  => null
            ], JsonResponse::HTTP_OK);
        }
        
        return $this->render('@quick_coding.view/component/dataTables.html.twig', ["title" => $title, "column" => $view, "path" => $path]);
    }
    
    function generateForm($title, $entityID, $formId, $id=null, $edit=true){
        $repository = $this->getDoctrine()->getRepository($entityID);
        $request = $this->get('request_stack')->getCurrentRequest();
        $path = $this->generateUrl($request->attributes->get('_route'), []);
        
        $item = $id ? $repository->find($id) : new $entityID();
        $form = $this->createForm($formId, $item, ['disabled' => !$edit]);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($item);
            $em->flush();
            
            return $this->redirect($path);
        }
        
        return $this->render("@quick_coding.view/component/form.html.twig", [
            "item" => $item,
            "form" => $form->createView(),
            "config" => ["title" => $title, "path" => $path]
        ]);
    }
    
    function remove($entityID, $id, $route){
        $item = $this->getDoctrine()->getRepository($entityID)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($item);
        $em->flush();
        
        if($this->get('request_stack')->getCurrentRequest()->isXmlHttpRequest()){
            return $this->json(['success' => true], JsonResponse::HTTP_OK);
        }else{
            return $this->redirectToRoute($route);
        }
    }
    
    function data($entityID, $order, $page, $perPage, $search){
        $repository = $this->getDoctrine()->getRepository($entityID);
        
        $data = $repository->createQueryBuilder('t');
        
        if($search){
            foreach($column as $col){$data->orWhere('t.' . $col['name'] . ' LIKE :' . $col['name'])->setParameter( $col['name'], '%' . $search . '%');}
        }
        
        if(count($order) == 2){$data->orderBy('t.' . $order[0], $order[1]);}
        
        return $this->knpPaginator->paginate($data->getQuery()->getArrayResult(), $page, $perPage);
    }
    
    function dataTable($entityID, $column, $orders, $length, $start, $search){
        $repository = $this->getDoctrine()->getRepository($entityID);
        
        $data = $repository->createQueryBuilder('t');
        $total = $repository->createQueryBuilder('t')->select('count(t.id)');
        
        if($search){
            foreach($column as $col){
                $data->orWhere('t.' . $col['name'] . ' LIKE :' . $col['name'])->setParameter( $col['name'], '%' . $search . '%');
                $total->orWhere('t.' . $col['name'] . ' LIKE :' . $col['name'])->setParameter( $col['name'], '%' . $search . '%');
            }
        }
        
        $total = $total->getQuery()->getSingleScalarResult();
        
        foreach($orders as $order){
            $data->orderBy('t.' . $column[$order['column']]['name'], $order['dir']);
        }
        
        $data = $data->setMaxResults($length)->setFirstResult($start)->getQuery()->getResult();
        
        return ['total' => $total, 'data' => $data];
    }
}  