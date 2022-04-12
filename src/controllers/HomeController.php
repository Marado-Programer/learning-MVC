<?php

/**
 * 
 */

class HomeController extends MainController
{
    public $userAssociations;

    public function __construct(
        $parameters = array(),
        $title = 'index',
        $permissions = PermissionsManager::P_ZERO
    ) {
        parent::__construct($parameters, $title, $permissions);
        $this->userAssociations = new AssociationsList();
    }

    protected function indexMain()
    {
        if (!$this->userSession->user->loggedIn)
            return;

        $this->model = $this->loadModel('home/HomeModel');

        $this->model->getUserAssociations();
        require VIEWS_PATH . '/home/home.php';
    }
}

