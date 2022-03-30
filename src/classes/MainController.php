<?php

/**
 *
 */

class MainController extends UserLogin
{
    public $db;
    public $passhash;
    public $title;
    public $loginRequired = false;
    public $premissionRequired = 'any';
    public $parameters = array();

    public function __construct($parameters = array())
    {
        $this->db = new SystemDB();
        $this->passhash = new PasswordHash(8, false);
        $this->parameters = $parameters;
        $this->checkUserLogin();
    }

    public function loadModel($model = false)
    {
        if (!$model)
            return;

        $model = strtolower($model);

        $modelsPath = ROOTPATH . "/src/models/$model.php";
        if (file_exists($modelsPath)) {
            require_once $modelPath;

            $model = explode('/\//', $model);
            $model = end($model);

            if (class_exists($model))
                return new $model($this->db, $this);

            return;
        }
    }

    public function checkUserLogin()
    {

    }
}

