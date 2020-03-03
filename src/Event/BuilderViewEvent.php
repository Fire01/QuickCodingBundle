<?php

namespace Fire01\QuickCodingBundle\Event;

use Symfony\Component\EventDispatcher\EventDispatcher;

class BuilderViewEvent extends EventDispatcher
{
    private $data;
    private $total;

    public function __construct($data, $total)
    {
        $this->setData($data);
        $this->setTotal($total);
    }
    
    public function getData()
    {
        return $this->data;
    }
    
    public function setData($data)
    {
        $this->data = $data;
    }
    
    public function getTotal()
    {
        return $this->total;
    }
    
    public function setTotal($total)
    {
        $this->total = $total;
    }
}
