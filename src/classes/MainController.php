<?php

/**
 *
 */

abstract class MainController
{
    public $userSession;
    public $user;
    
    protected $db;
    protected $title;
    protected $loginRequired;
    protected $premissionsRequired;

    public $parameters;

    protected $model;

    public $tools;

    public function __construct(
        $parameters = [],
        $title = 'index',
    ) {
        try {
            $this->db = new DBConnection();
            $this->db->checkAccess();
        } catch (Exception $e) {
            die($e);
        }

        $this->tools = UsersManager::getTools();
        $this->userSession = new UserSession($this->db);
        $this->user = UserSession::getUser();

        $this->parameters = $parameters;
        $this->title = $title;

        $this->loginRequired = false;
        $this->premissionsRequired = PermissionsManager::P_ZERO;
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
                $this->model = new $model($this->db, $this);

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
        $this->tools->getPremissionsManager()->checkUserPermissions(
            $this->user,
            $this->premissionsRequired
        ) OR $this->user->isLoggedIn() && !$this->loginRequired OR exit();

        require VIEWS_PATH . '/includes/head.php';
        require VIEWS_PATH . '/includes/nav.php';

        $this->indexMain();

        require VIEWS_PATH . '/includes/footer.php';
    }

    protected abstract function indexMain();
}

