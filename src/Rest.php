<?php 
namespace Fire01\QuickCodingBundle;

use Fire01\QuickCodingBundle\Entity\View;

class Rest {
    
    protected $view;
    protected $doctrine;
    
    function __construct($doctrine){
        $this->doctrine = $doctrine;
    }
    
    function set($config)
    {
        $this->view = new View($config);
        
        return $this;
    }
    
    function getConfig() :View
    {
        return $this->view;
    }
     
    function generate()
    {
        $repository = $this->doctrine->getRepository($this->view->getEntity());
        
        $data = $repository->createQueryBuilder('t');
        if(count($this->view->getSelect())){
            $select = array_map(function($select){return strpos($select, ".") !== false ? $select : "t." . $select;},$this->view->getSelect());
            $data->select(implode(", ", $select));
        }
        
        if($this->view->getTotal())    $total = $repository->createQueryBuilder('t')->select('count(t.id)');
        else $total = 0;
        
        foreach($this->view->getJoin() as $column => $alias){
            $data->leftJoin("t." . $column, $alias);
            if($total)  $total->leftJoin("t." . $column, $alias);
        }
        
        if(count($this->view->getSearch()) && $this->view->getQ()){
            $counter = 0;
            foreach($this->view->getSearch() as $column){
                if(strpos($column, ".") !== false){
                    $relation = explode(".", $column);
                    $searchCondition = $relation[0] . "." . $relation[1] . " LIKE :param_" . $counter;
                    $param = "param_" . $counter;
                    $counter++;
                }else{
                    $searchCondition = "t." . $column . " LIKE :" . $column;
                    $param = $column;
                }
                
                $data->orWhere($searchCondition)->setParameter($param, "%" . $this->view->getQ() . "%");
                if($total)  $total->orWhere($searchCondition)->setParameter($param, "%" . $this->view->getQ() . "%");
            }
        }
        
        if($total)    $total = $total->getQuery()->getSingleScalarResult();
        
        foreach($this->view->getOrders() as $key => $order){
            $key = strpos($key, ".") !== false ? $key : "t." . $key;
            $data->addOrderBy($key, $order);
        }

        $data = $data->setMaxResults($this->view->getLength())->setFirstResult($this->view->getFirstResult())->getQuery()->getResult();
        
        return $total ? ['total' => $total, 'data' => $data] : $data;
    }
    
}