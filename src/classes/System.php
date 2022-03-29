<?php

/**
 * MVC
 */

class System
{
    private $controller;
    private $action;
    private $parameters;
    private $notFound = ROOTPATH . '/404.php';

    public function __construct()
    {
        $this->getURLData();

        if (!$this->controller) {
            require_once CONTROLLERSPATH . '/HomeController.php';
            $this->controller = new HomeController();

            $this->controller->index();
            return;
        }

        if (!file_exists(CONTROLLERSPATH . "/$this->controller.php")) {
            require_once $this->notFound;

            return;
        }

        require_once CONTROLLERSPATH . "/$this->controller.php";

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

        if (!$this->action && method_exists($this->controller, 'index')) {
            $this->controller->index($this->parameters);

            return;
        }

        require_once $this->notFound;

        return;
    }

    public function getURLData()
    {
        if (isset($_GET['path'])) {
            $path = $_GET['path'];
            $path = rtrim($path, '/\//');
            $path = filter_var($path, FILTER_SANITIZE_URL);
            $path = explode('/', $path);

            $this->controller = checkArray($path, 0);
            $this->controller .= 'Controller';

            $this->action = checkArray($path, 1);

            if (checkArray($path, 2)) {
                unset($path[0]);
                unset($path[1]);

                $this->parameters = array_values($path);
            }
        }
    }
}

