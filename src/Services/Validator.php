<?php 
namespace Fire01\QuickCodingBundle\Services;

use Fire01\QuickCodingBundle\Entity\Config;

class Validator {
    
    public static function build(Config $config){
        if(!$config->getEntity()) throw new \Exception('Entity not found. Please set "entity" on Builder config!');
        if(!$config->getForm()) throw new \Exception('Form not found. Please set "form" on Builder config!');
        if(!$config->getView()) throw new \Exception('View not found. Please set "view" on Builder config!');
    }
    
    public static function view(Config $config){
        if(!$config->getEntity()) throw new \Exception('Entity not found. Please set "entity" on Builder config!');
        if(!$config->getView()) throw new \Exception('View not found. Please set "view" on Builder config!');
        if(!$config->getPathView()) throw new \Exception('View not found. Please set "path.view" on Builder config!');
        if(!$config->getPathForm()) throw new \Exception('Form not found. Please set "path.form" on Builder config!');
        if(!$config->getPathRemove()) throw new \Exception('Remove not found. Please set "path.remove" on Builder config!');
    }
    
    public static function form(Config $config){
        if(!$config->getEntity()) throw new \Exception('Entity not found. Please set "entity" on Builder config!');
        if(!$config->getForm()) throw new \Exception('Form not found. Please set "form" on Builder config!');
        if(!$config->getPathView()) throw new \Exception('View not found. Please set "path.view" on Builder config!');
        if(!$config->getPathForm()) throw new \Exception('Form not found. Please set "path.form" on Builder config!');
    }
    
    public static function remove(Config $config, $isAjax=false){
        if(!$config->getEntity()) throw new \Exception('Entity not found. Please set "entity" on Builder config!');
        if(!$isAjax && !$config->getPathView()) throw new \Exception('View not found. Please set "path.view" on Builder config!');
    }
    
}