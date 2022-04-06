<?php

/**
 * MVC
 */

class System
{
    private $controller;
    private $action;
    private $parameters;
    private $notFound = NOT_FOUND;

    public function __construct()
    {
        $this->fetchGETPath();

        // Set default controller if controller it's unset
        if (!$this->controller) {
            require_once CONTROLLERS_PATH . '/HomeController.php';

            $this->controller = new HomeController();

            // All controllers will call the index() by default
            $this->controller->index();

            return;
        }

        if (!file_exists(CONTROLLERS_PATH . "/$this->controller.php")) {
            require_once $this->notFound;

            return;
        }

        require_once CONTROLLERS_PATH . "/$this->controller.php";

        $this->controller = preg_replace('/[^A-Z]/i', '', $this->controller);

        if (!class_exists($this->controller)) {
            require_once $this->notFound;

            return;
        }

        $this->controller = new $this->controller($this->parameters);

        $this->action = preg_replace('/[^A-Z]/i', '', $this->action);

        if (method_exists($this->controller, $this->action)) {
            $this->controller->{$this->action}($this->parameters);

            return;
        }

        // Again, all controllers will call the index() by default
        if (!$this->action && method_exists($this->controller, 'index')) {
            $this->controller->index($this->parameters);

            return;
        }

        require_once $this->notFound;

        return;
    }

    /**
     * Uses the $_GET['path'] to set the controller, action and parameters.
     * 
     * So the URL needs to be like this (with .htaccess): 
     * http://www.example.com/controller/action/parameter1/parameter2/parameterN
     */
    public function fetchGETPath()
    {
        if (isset($_GET['path'])) {
            $path = $_GET['path'];
            $path = rtrim($path, '/\//');
            $path = filter_var($path, FILTER_SANITIZE_URL);
            $path = explode('/', $path);

            $this->controller = checkArray($path, 0);
            $this->controller = ucfirst(strtolower($this->controller));
            $this->controller .= 'Controller';

            $this->action = checkArray($path, 1);

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
}

