<?php

/**
 *
 */

class ProjectsController extends MainController
{
    public $loginRequired = false;
    public $permissionsRequired;
    public $previousPage = false;

    function index()
    {
        $this->title = 'Projects';

        $this->model = $this->loadModel('projects/ProjectsAdmModel');

        require ROOT_PATH . '/public/views/includes/header.php';
        require ROOT_PATH . '/public/views/includes/nav.php';

        require ROOT_PATH . '/public/views/projects/home.php';

        require ROOT_PATH . '/public/views/includes/footer.php';
    }

    public function adm()
    {
        $this->title = 'Gerenciar Projetos';

        $permissionsRequired = PermissionsManager::P_EDIT_PROJECTS | PermissionsManager::P_DELETE_PROJECTS;

        $this->model = $this->loadModel('projects/ProjectsAdmModel');

        require ROOT_PATH . '/public/views/includes/header.php';
        require ROOT_PATH . '/public/views/includes/nav.php';

        if (!$this->userSession->user->loggedIn) {
            $this->userSession->logout(true);
            $this->userSession->checkUserLogin();
            return;
        }

        if (!$this->userSession->permissionManager->checkPermissions(
            $this->permissionsRequired,
            $this->userSession->user->permissions
        ))
            echo 'no permission';
        else
            require ROOT_PATH . '/public/views/projects/adm.php';

        require ROOT_PATH . '/public/views/includes/footer.php';
    }
}

