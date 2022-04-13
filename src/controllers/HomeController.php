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

        if (isset($_POST['association'])) {
            $data = $_POST['association'];
            unset($_POST['association']);

            if ($data['redirect'] == 'page') {
                $this->userSession->redirect(HOME_URI . '/@' . $data['name']);
                return;
            } elseif ($data['redirect'] == 'admin') {
                $this->userSession->redirect(HOME_URI . '/!' . $data['name']);
                return;
            }
        }

        $this->model = $this->loadModel('home/HomeModel');

        $this->model->getUserAssociations();

        require VIEWS_PATH . '/home/home.php';
    }
}

