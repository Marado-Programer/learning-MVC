<?php

/**
 *
 */

abstract class MainController
{
    protected $db;
    public $userSession;
    protected $title;
    protected $loginRequired = false;
    protected $premissionsRequired;
    public $parameters = array();
    protected $model;

    public function __construct(
        $parameters = array(),
        $title = 'index',
        $permissions = PermissionsManager::P_ZERO,
        $loginRequired = false
    ) {
        $this->db = new SystemDB();
        $this->userSession = new UserSession($this->db);
        $this->parameters = $parameters;
        $this->title = $title;
        $this->premissionsRequired = $permissions;
        $this->loginRequired = $loginRequired;
    }

    protected function loadModel($model = false)
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

    final public function index()
    {
        $this->parameters = func_num_args() >= 1 ? func_get_arg(0) : array();

        require VIEWS_PATH . '/includes/header.php';
        require VIEWS_PATH . '/includes/nav.php';

        $this->indexMain();

        require VIEWS_PATH . '/includes/footer.php';
    }

    protected abstract function indexMain();
}

