<?php

/**
 *
 */

abstract class ControllerGenerator
{
    protected $controller;
    protected $action;
    protected $parameters;

    abstract public function factoryMethod();

    public function act()
    {
        $controller = $this->factoryMethod();

        if (method_exists($controller, $this->action)) {
            $controller->{$this->action}($this->parameters);

            return;
        }

        // Again, all controllers will call the index() by default
        if (!$controller->action && method_exists($controller, 'index')) {
            $controller->index($this->parameters);

            return;
        }

        require_once NOT_FOUND;

        return;
    }
}

