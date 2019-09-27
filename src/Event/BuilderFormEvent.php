<?php

namespace Fire01\QuickCodingBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class BuilderFormEvent extends Event
{
    private $entity;
    private $form;

    public function __construct($entity, $form)
    {
        $this->setEntity($entity);
        $this->setForm($form);
    }
    
    public function getEntity()
    {
        return $this->entity;
    }
    
    public function setEntity($entity)
    {
        $this->entity = $entity;
    }
    
    public function getForm()
    {
        return $this->form;
    }
    
    public function setForm($form)
    {
        $this->form = $form;
    }
}
