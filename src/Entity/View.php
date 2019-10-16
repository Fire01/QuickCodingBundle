<?php

namespace Fire01\QuickCodingBundle\Entity;

class View
{
  
    protected $entity;
    protected $select = [];
    protected $conditions = [];
    protected $orders = [];
    protected $search = [];
    protected $q;
    protected $total=false;
    protected $page = 1;
    protected $length = 25;
    protected $alias = "t";
    protected $join = [];
    
    function __construct($config){
        
        if(isset($config['entity']))    $this->setEntity($config['entity']);
        if(isset($config['select']))    $this->setSelect($config['select']);
        if(isset($config['conditions']))$this->setConditions($config['conditions']);
        if(isset($config['orders']))    $this->setOrders($config['orders']);
        if(isset($config['page']))      $this->setPage($config['page']);
        if(isset($config['length']))    $this->setLength($config['length']);
        if(isset($config['search']))    $this->setSearch($config['search']);
        if(isset($config['q']))         $this->setQ($config['q']);
        if(isset($config['total']))     $this->setTotal($config['total']);
        if(isset($config['alias']))     $this->setAlias($config['alias']);
        
        $this->setJoin();
        
        return $this;
    }

    function getEntity(): ?string
    {
        return $this->entity;
    }

    function setEntity(?string $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
    
    function getSelect(): ?array
    {
        return $this->transformToAlias($this->select);
    }
    
    function setSelect(array $select): self
    {
        $this->select = $select;
        
        return $this;
    }
    
    function getConditions(): ?array
    {
        return $this->transformToAlias($this->conditions);
    }
    
    function setConditions(array $conditions): self
    {
        $this->conditions = $conditions;
        
        return $this;
    }

    function getOrders(): ?array
    {
        return $this->transformToAlias($this->orders);
    }

    function setOrders(?array $orders): self
    {
        $this->orders = $orders;

        return $this;
    }

    function getSearch(): ?array
    {
        return count($this->search) ? $this->transformToAlias($this->search) : $this->transformToAlias($this->select);
    }

    function setSearch(?array $search): self
    {
        $this->search = $search;

        return $this;
    }

    function getQ(): ?string
    {
        return $this->q;
    }

    function setQ(?string $q): self
    {
        $this->q = $q;

        return $this;
    }

    function getTotal(): ?bool
    {
        return $this->total;
    }

    function setTotal(?bool $total): self
    {
        $this->total = $total;

        return $this;
    }
    
    function getPage(): ?int
    {
        return $this->page;
    }
    
    function setPage(?int $page): self
    {
        $this->page = $page;
        
        return $this;
    }
    
    function getLength(): ?int
    {
        return $this->length;
    }
    
    function setLength(?int $length): self
    {
        $this->length = $length;
        
        return $this;
    }
    
    function getAlias(): ?string
    {
        return $this->alias;
    }
    
    function setAlias(?string $alias): self
    {
        $this->alias = $alias;
        
        return $this;
    }
    
    function getJoin(): ?array
    {
        return $this->join;
    }
    
    function setJoin(){
        $counter = 0;
        $columns = array_merge($this->select, array_keys($this->orders), $this->search);
        foreach($columns as $column){
            if(strpos($column, ".") !== false){
                $relation = explode(".", $column);
                if(!isset($this->join[$relation[0]])){
                    $this->join[$relation[0]] = 'alias_' . $counter;
                    $counter++;
                }
            }
        }
    }
    
    function getFirstResult(){
        return ($this->getPage() - 1) * $this->getLength();
    }
    
    function transformToAlias(array $data){
        $result = [];
        foreach($data as $key => $column){
            $type = is_array($column) ? 3 : (is_numeric($key) ? 1 : 2);
            
            switch ($type){
                case 1:
                    $result[] = $this->getRelationAlias($column);
                    break;
                case 2:
                    $result[$this->getRelationAlias($key)] = $column;
                    break;
                case 3:
                    $column[0] = $this->getRelationAlias($column[0]);
                    $result[] = $column;
                    break;
            }
        }
        
        return $result;
    }
    
    function getRelationAlias($string){
        $arr = explode(".", $string);
        return isset($this->join[$arr[0]]) ? $this->join[$arr[0]] . "." . $arr[1] : $this->getAlias() . "." . $string;
    }
}
