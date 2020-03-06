<?php 
namespace Fire01\QuickCodingBundle\Builder\Components;

class Reset extends Button {
    
    function __construct()
    {
        $this->setType(Action::RESET);
    }
}