<?php

namespace Fire01\QuickCodingBundle\Entity;

class View
{
  
    protected $entity;
    protected $selects = [];
    protected $orders = [];
    protected $search = [];
    protected $q;
    protected $total=false;
    protected $page;
    protected $length;
    
    function __construct($config){
        
        if(isset($config['entity']))    $this->setEntity($config['entity']);
        if(isset($config['orders']))    $this->setOrders($config['orders']);
        if(isset($config['page']))      $this->setPage($config['page']);
        if(isset($config['length']))    $this->setLength($config['length']);
        if(isset($config['search']))    $this->setSearch($config['search']);
        if(isset($config['q']))         $this->setQ($config['q']);
        if(isset($config['total']))     $this->setTotal($config['total']);
    }

    public function getEntity(): ?string
    {
        return $this->entity;
    }

    public function setEntity(?string $entity): self
    {
        $this->entity = $entity;

        return $this;
    }
    
    public function getSelect(): ?array
    {
        return $this->select;
    }
    
    public function setSelects(array $select): self
    {
        $this->select = $select;
        
        return $this;
    }

    public function getOrders(): ?array
    {
        return $this->orders;
    }

    public function setOrders(?array $orders): self
    {
        $this->orders = $orders;

        return $this;
    }

    public function getSearch(): ?array
    {
        return $this->search;
    }

    public function setSearch(?array $search): self
    {
        $this->search = $search;

        return $this;
    }

    public function getQ(): ?string
    {
        return $this->q;
    }

    public function setQ(?string $q): self
    {
        $this->q = $q;

        return $this;
    }

    public function getTotal(): ?bool
    {
        return $this->total;
    }

    public function setTotal(?bool $total): self
    {
        $this->total = $total;

        return $this;
    }
    
    public function getPage(): ?int
    {
        return $this->page;
    }
    
    public function setPage(int $page): self
    {
        $this->page = $page;
        
        return $this;
    }
    
    public function getLength(): ?int
    {
        return $this->length;
    }
    
    public function setLength(int $length): self
    {
        $this->length = $length;
        
        return $this;
    }
}
