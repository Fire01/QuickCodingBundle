<?php

namespace Fire01\QuickCodingBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class BuilderEvent extends Event
{
    private $entity;
    private $form;

    public function __construct($form, $entity)
    {
        $this->setForm($form);
        $this->setEntity($entity);
    }
    
    public function getForm()
    {
        return $this->form;
    }
    
    public function setForm($form)
    {
        $this->form = $form;
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
