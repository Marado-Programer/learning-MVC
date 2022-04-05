<?php

/**
 *
 */

class ProjectsController extends MainController
{
    public $loginRequired = false;
    public $permissionRequired;
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

        $permission_required = 'gerir-projetos';
        if (!$this->loggedIn) {
            $this->logout();
            $this->checkUserLogin();
            return;
        }

        if (!$this->check_permissions(
            $this->permission_required,
            $this->userdata['user_permissions']
        )) {
            echo 'no permission';
            return;
        }

        $this->model = $this->loadModel('projects/ProjectsAdmModel');

        require ROOT_PATH . '/public/views/includes/header.php';
        require ROOT_PATH . '/public/views/includes/nav.php';

        require ROOT_PATH . '/public/views/projects/home.php';

        require ROOT_PATH . '/public/views/includes/footer.php';
    }
}

