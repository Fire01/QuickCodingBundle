<?php 
namespace Fire01\QuickCodingBundle\Builder\Components;

class Actionbars {

    protected $actions = [];

    /**
     * Get the value of actions
     */ 
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Get the actions by id
     */ 
    public function getById($id)
    {
        $key = array_search($id, array_column($this->actions, 'id'));
        return $key !== false ? $this->actions[$key] : null;
    }

    /**
     * Set the value of actions
     *
     * @return  self
     */ 
    public function setActions($actions)
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * Set the value of actions
     *
     * @return  self
     */ 
    public function addAction(Action $action)
    {
        $this->actions[] = $action;

        return $this;
    }

    /**
     * Clear actions
     *
     * @return  self
     */ 
    public function clear()
    {
        $this->actions = [];

        return $this;
    }
}