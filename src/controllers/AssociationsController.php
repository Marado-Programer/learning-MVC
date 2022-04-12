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
        $permissions = PermissionsManager::P_VIEW_ASSOCIATIONS,
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

        $this->model->search();
        if (isset($_POST['create']))
            $this->model->createAssociation();

        require VIEWS_PATH . '/associations/index.php';
    }
}

