<?php

/**
 * 
 */

class AssociationsController extends MainController
{
    public $associations;
    public $association;

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
            !UsersManager::getTools()->getPremissionsManager()->checkPermissions(
                UserSession::getUser()->getPermissions(),
                $this->premissionsRequired,
                false
            )
        )
            return;

        $this->loadModel('associations/AssociationsModel');

        if (isset($_POST['association'])) {
            $data = $_POST['association'];
            unset($_POST['association']);
            if ($data['action'] == 'enter')
                $this->model->enterAssocition($data['id']);
        }

        if (isset($_POST['create']))
            $this->model->createAssociation();

        $this->model->search();

        require VIEWS_PATH . '/associations/index.php';
    }

    public function page()
    {
        echo "page";
    }

    public function admni()
    {
        if (!UserSession::getUser()->loggedIn)
            return;

        if (!isset($this->parameters[0]))
            return;

        $this->loadModel('associations/AssociationsAdmniModel');

        $this->association = $this->model->getAssociationByNickname($this->parameters[0]);

        if (!isset($this->association))
            return;

        $permissions = $this->model->userAdmniPermissions(UserSession::getUser(), $this->association);

        if (!UsersManager::getTools()->permissionManager->checkPermissions(
            $permissions,
            PermissionsManager::AP_PARTNER_ADMNI,
            false
        ))
            return;

        if (isset($_POST['create']))
            $this->model->createNews($this->association);

        if (isset($_POST['event']))
            $this->model->createEvent($this->association);

        if (isset($_POST['image']))
            $this->model->createImage($this->association);

        require VIEWS_PATH . '/includes/header.php';
        require VIEWS_PATH . '/includes/nav.php';

        require VIEWS_PATH . '/associations/admni.php';

        require VIEWS_PATH . '/includes/footer.php';
    }
}

