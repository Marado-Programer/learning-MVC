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
        $this->loadModel('home/HomeModel');

        $this->model->getUserAssociations();

        if (isset($_POST['payQuota']['pay']))
            $this->model->payQuota((int) $_POST['payQuota']['association']);

        if (isset($_POST['create']))
            $this->model->createNews();

        require VIEWS_PATH . '/home/home.php';
    }
}

