<?php

/**
 * MVC
 */

class System extends ControllerGenerator
{
    public function __construct()
    {
        $this->fetchGETPath();

        if (!file_exists(CONTROLLERS_PATH . "/$this->controller.php")) {
            require_once NOT_FOUND;

            return;
        }

        require_once CONTROLLERS_PATH . "/$this->controller.php";

        $this->controller = preg_replace('/[^A-Z]/i', '', $this->controller);

        if (!class_exists($this->controller)) {
            require_once NOT_FOUND;

            return;
        }

        $this->action = preg_replace('/[^A-Z]/i', '', $this->action);
    }

    public function factoryMethod()
    {
        return new $this->controller(
            $this->parameters,
            stristr($this->controller, 'Controller', true)
            . ($this->action == 'index' ? '' : ' - ' . $this->action)
        );
    }

    /**
     * Uses the $_GET['path'] to set the controller, action and parameters.
     * 
     * So the URL needs to be like this (with .htaccess): 
     * http://www.example.com/controller/action/parameter1/parameter2/parameterN
     */
    public function fetchGETPath()
    {
        $path = $_GET['path'] ?? 'Home/index';
        $path = rtrim($path, '/\//');
        $path = filter_var($path, FILTER_SANITIZE_URL);
        $path = explode('/', $path);

        $this->controller = checkArray($path, 0) ?? 'Home';
        $this->controller = explode('-', $this->controller);
        $this->controller = array_map('ucfirst', $this->controller);
        $this->controller = implode('', $this->controller);
        $this->controller .= 'Controller';

        $this->action = checkArray($path, 1) ?? 'index';

        if (checkArray($path, 2)) {
            /** 
             * we make the this 2 null so when we use array_values()
             * they aren't returned in the array.
             */
            unset($path[0]);
            unset($path[1]);

            $this->parameters = array_values($path);
        }
    }
}

