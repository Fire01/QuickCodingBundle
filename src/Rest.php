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
        
        $data = $repository->createQueryBuilder($this->view->getAlias());
        if(count($this->view->getSelect())){
            $data->select(implode(", ", $this->view->getSelect()));
        }
        
        if($this->view->getTotal())    $total = $repository->createQueryBuilder($this->view->getAlias())->select("count(" . $this->view->getAlias() . ".id)");
        else $total = 0;
        
        foreach($this->view->getJoin() as $column => $alias){
            $data->leftJoin($this->view->getAlias() . "." . $column, $alias);
            if($total)  $total->leftJoin($this->view->getAlias() . "." . $column, $alias);
        }
        
        $counter = 0;
        if(count($this->view->getConditions())){
            foreach($this->view->getConditions() as $condition){
                $param = "param_" . $counter;
                $value = $condition[2];
                $condition[2] = ":" . $param;
                $connector = isset($condition[3]) ? $condition[3] : 'and';
                if(isset($condition[3]))    array_pop($condition);
                
                $conditions = implode(" ", $condition);
                
                if(strtolower($connector) == 'or'){
                    $data->orWhere($conditions)->setParameter($param, $value);
                    if($total)  $total->orWhere(implode(" ", $condition))->setParameter($param, $value);
                }else{
                    $data->andWhere($conditions)->setParameter($param, $value);
                    if($total)  $total->andWhere(implode(" ", $condition))->setParameter($param, $value);
                }
                
                $counter++;
            }
        }
        
        if(count($this->view->getSearch()) && $this->view->getQ()){
            $param = "param_" . $counter;
            $searchCondition = implode(" LIKE :" . $param . " OR ", $this->view->getSearch()) . " LIKE :" . $param;
            $data->andWhere($searchCondition)->setParameter($param, "%" . $this->view->getQ() . "%");
            if($total)  $total->andWhere($searchCondition)->setParameter($param, "%" . $this->view->getQ() . "%");
            
            $counter++;
        }
        
        if($total)    $total = $total->getQuery()->getSingleScalarResult();
        
        foreach($this->view->getOrders() as $key => $order){
            $data->addOrderBy($key, $order);
        }
        
        $data = $data->setMaxResults($this->view->getLength())->setFirstResult($this->view->getFirstResult())->getQuery()->getResult();
        
        return $total ? ['total' => $total, 'data' => $data] : $data;
    }
    
}