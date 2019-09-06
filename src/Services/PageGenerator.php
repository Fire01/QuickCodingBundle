<?php  
namespace Fire01\QuickCodingBundle\Services;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PageGenerator{ 
    
    private $request;
    private $router;
    private $repository;
    private $EM;
    private $twig;
    
    private $name;
    private $title;
    private $url = [];
    
    public function __construct(RouterInterface $router, RequestStack $request, EntityManagerInterface $entityManager, Environment $twig)
    {
        $this->request = $request;
        $this->router = $router;
        $this->EM = $entityManager;
        $this->twig = $twig;
    }
    
    function load($config){
        $this->name = $config['name'];
        $this->title = isset($config['title']) && $config['title'] ? $config['title'] : ucfirst($this->name);
        $this->repository = $config['repository'];
        
        $controller = explode("::", $this->request->getCurrentRequest()->attributes->get('_controller'))[0];
        foreach($this->router->getRouteCollection()->all() as $route){
            $ctrl = explode("::", $route->getDefaults()['_controller']);
            if($controller == $ctrl[0]){
                 switch($ctrl[1]){
                     case "view":
                         $this->url['view'] = $route->getPath();
                         break;
                     case "form":
                         $urlForm = str_replace("{id}", "", $route->getPath());
                         $this->url['new'] = str_replace("{action}/", "new", $urlForm);
                         $this->url['open'] = str_replace("{action}", "get", $urlForm) . "{id}";
                         $this->url['edit'] = str_replace("{action}", "edit", $urlForm) . "{id}";
                         $this->url['remove'] = str_replace("{action}", "remove", $urlForm) . "{id}";
                         break;
                 }
            }
        }
    }
    
    function generateView($column, Response $response=null){
        $params = $this->request->getCurrentRequest()->query->all();
        $config = [
            "title" => $this->title,
            "url"   => $this->url,
            "column" => $column
        ];
        
        if(isset($params['type']) && $params['type'] == 'json'){
            $dataTables = $this->dataTable($config["column"], $params["order"], $params["length"], $params["start"], $params["search"]["value"]); 
            return new JsonResponse([
                "draw"  => $params["draw"],
                "recordsTotal"  => $dataTables["total"],
                "recordsFiltered"  => $dataTables["total"],
                "data"  => $dataTables["data"],
                "error"  => null
            ], JsonResponse::HTTP_OK);
        }
        
        return new Response($this->twig->render("@quick_coding.view/component/view.html.twig", $config));
    }
    
    function generateForm($data){
        $item = $data['id'] ? $this->repository->find($data['id']) : $data['model'];
        $form = $data['form']->setData($item);
        $form->handleRequest($this->request->getCurrentRequest());
        
        if($form->isSubmitted() && $form->isValid()) {
            $this->EM->persist($item);
            $this->EM->flush();
            
            return new RedirectResponse($this->url['view']);
        }
        
        $config = [
            "title" => $this->title,
            "cancel" => ["url" => $this->url['view']],
        ];
        if($data["id"]) $config["edit"] = ["url" => str_replace("{id}", $data["id"], $this->url['edit'])];
        
        return new Response($this->twig->render("@quick_coding.view/component/form.html.twig", 
            [
                "item" => $item,
                "form" => $form->createView(),
                "config" => $config
            ]
        ));
    }
    
    function remove($id){
        $item = $this->repository->find($id);
        $this->EM->remove($item);
        $this->EM->flush();
        
        return new JsonResponse(['success' => true], JsonResponse::HTTP_OK);
    }
    
    function dataTable($column, $orders, $length, $start, $search){
        $data = $this->repository->createQueryBuilder('t');
        $total = $this->repository->createQueryBuilder('t')->select('count(t.id)');
        
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