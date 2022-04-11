<?php

/**
 * 
 */

class AssociationsController extends MainController
{
    public function indexMain()
    {
        $this->premissionsRequired = PermissionsManager::P_VIEW_ASSOCIATIONS;

        if (
            !$this->userSession->permissionManager->checkUserPermissions(
                $this->userSession->user,
                $this->premissionsRequired,
                false
            )
        )
            return;

        $this->model = $this->loadModel('associations/AssociationsCreateModel');

        if (isset($_POST['create']))
            $this->model->createAssociation();

        require VIEWS_PATH . '/associations/index.php';
    }
}

