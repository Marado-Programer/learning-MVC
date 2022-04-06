<?php

/**
 *
 */

class MainController
{
    public $userSession;
    public $db;
    public $title;
    public $loginRequired = false;
    public $premissionsRequired;
    public $parameters = array();
    public $model;

    public function __construct($parameters = array())
    {
        $this->db = new SystemDB();
        $this->userSession = new UserSession($this->db);
        $this->premissionsRequired = PermissionsManager::P_ZERO;
        $this->parameters = $parameters;
        $this->userSession->checkUserLogin();
    }

    public function loadModel($model = false)
    {
        if (!$model)
            return;

        $model = rtrim($model, '/\//');

        $modelPath = ROOT_PATH . "/src/models/$model.php";
        if (file_exists($modelPath)) {
            require_once $modelPath;

            $model = explode('/', $model);
            $model = end($model);

            if (class_exists($model))
                return new $model($this->db, $this);

            return;
        }
    }

    /*
    protected function defineGotoURL()
    {
        $_SESSION['gotoURL'] = urlencode($_SERVER['PHP_SELF']);
    }
    */
}

