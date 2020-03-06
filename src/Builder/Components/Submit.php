<?php 
namespace Fire01\QuickCodingBundle\Builder\Components;

class Submit extends Button {
    
    function __construct()
    {
        $this->setType(Action::SUBMIT);
    }
}