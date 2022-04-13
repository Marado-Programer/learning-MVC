<?php

/**
 * 
 */

class AssociationsController extends MainController
{
    public $associations;

    public function __construct(
        $parameters = array(),
        $title = 'index',
        $permissions = PermissionsManager::P_VIEW_ASSOCIATIONS
    ) {
        parent::__construct($parameters, $title, $permissions);
        $this->associations = new AssociationsList();
    }

    public function indexMain()
    {
        if (
            !UsersManager::getPermissionsManager()->checkUserPermissions(
                $this->userSession->user,
                $this->premissionsRequired,
                false
            )
        )
            return;

        $this->model = $this->loadModel('associations/AssociationsModel');

        if (isset($_POST['create']))
            $this->model->createAssociation();

        $this->model->search();

        require VIEWS_PATH . '/associations/index.php';
    }

    public function page()
    {
        echo "page";
    }

    public function admnistration()
    {
        print_r($this->parameters);
        if (!isset($this->parameters[0]))
            return;

        require VIEWS_PATH . '/includes/header.php';
        require VIEWS_PATH . '/includes/nav.php';

        $this->model = $this->loadModel('associations/AssociationsAdmniModel');

        $this->model->test();

        require VIEWS_PATH . '/includes/footer.php';
    }
}

