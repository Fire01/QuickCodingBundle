<?php

namespace Fire01\QuickCodingBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class BuilderEvent extends Event
{
    private $entity;
    private $request;

    public function __construct($request, $entity)
    {
        $this->setEntity($entity);
        $this->setRequest($request);
    }
    
    public function getRequest()
    {
        return $this->request;
    }
    
    public function setRequest($request)
    {
        $this->request = $request;
    }
    
    public function getEntity()
    {
        return $this->entity;
    }
    
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }
}
