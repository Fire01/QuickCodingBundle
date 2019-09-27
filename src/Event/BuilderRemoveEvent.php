<?php

namespace Fire01\QuickCodingBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class BuilderRemoveEvent extends Event
{
    private $entity;
    private $em;

    public function __construct($entity, $em)
    {
        $this->setEntity($entity);
        $this->setEm($em);
    }
    
    public function getEntity()
    {
        return $this->entity;
    }
    
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }
    
    public function getEm()
    {
        return $this->em;
    }
    
    public function setEm($em)
    {
        $this->em = $em;
    }
}
