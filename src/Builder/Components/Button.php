<?php 
namespace Fire01\QuickCodingBundle\Builder\Components;

class Button extends Action {

    protected $click;

    function __construct()
    {
        $this->setType(Action::BUTTON);
    }

    /**
     * Get the value of click
     */ 
    public function getClick()
    {
        return $this->click;
    }

    /**
     * Set the value of click
     *
     * @return  self
     */ 
    public function setClick($click)
    {
        $this->click = $click;

        return $this;
    }
}