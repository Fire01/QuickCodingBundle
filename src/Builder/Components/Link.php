<?php 
namespace Fire01\QuickCodingBundle\Builder\Components;

class Link extends Action{
    
    protected $href;
    protected $target;

    function __construct()
    {
        $this->setType(Action::LINK);
    }
}