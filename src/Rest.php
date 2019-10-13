<?php 
namespace Fire01\QuickCodingBundle;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Rest extends AbstractController{
    
    protected $entity;
    protected $page = 1;
    protected $length = 25;
    protected $orders = [];
    protected $search;
    protected $q;
    protected $join;
    protected $total=false;
    
    function set($config){
        $this->entity = $config['entity'];
        
        if(isset($config['orders']))    $this->orders = $config['orders'];
        if(isset($config['page']))      $this->page = $config['page'];
        if(isset($config['length']))   $this->length = $config['length'];
        if(isset($config['search']))    $this->search = $config['search'];
        if(isset($config['q']))    $this->q = $config['q'];
        if(isset($config['join']))      $this->search = $config['join'];
        if(isset($config['total']))      $this->total = $config['total'];
        
        return $this;
    }
     
    function generate(){
        $repository = $this->getDoctrine()->getRepository($this->entity);
        
        $data = $repository->createQueryBuilder('t');
        if($this->total)    $total = $repository->createQueryBuilder('t')->select('count(t.id)');
        else $total = 0;
        
        $counter = 0;
        if(count($this->search) && $this->q){
            foreach($this->search as $column){

                if(strpos($column, ".") !== false){
                    $relation = explode(".", $column);
                    $data->leftJoin("t." . $relation[0], 'a_' . $counter);
                    if($this->total)    $total->leftJoin("t." . $relation[0], 'a_' . $counter);
                    $searchCondition = 'a_' . $counter . "." . $relation[1] . " LIKE :p_" . $counter;
                    $column = "p_" . $counter;
                    $counter++;
                }else{
                    $searchCondition = "t." . $column . " LIKE :" . $column;
                }
                
                $data->orWhere($searchCondition)->setParameter($column, "%" . $this->q . "%");
                if($this->total)    $total->orWhere($searchCondition)->setParameter($column, "%" . $this->q . "%");
            }
        }
        
        if($this->total)    $total = $total->getQuery()->getSingleScalarResult();
        
        foreach($this->orders as $key => $order){$data->addOrderBy('t.' . $key, $order);}
        
        $data = $data->setMaxResults($this->length)->setFirstResult(($this->page - 1) * $this->length)->getQuery()->getResult();
        
        return $total ? ['total' => $total, 'data' => $data] : $data;
    }
    
}