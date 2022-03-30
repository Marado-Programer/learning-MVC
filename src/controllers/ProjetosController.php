<?php

/**
 *
 */

class ProjetosController extends MainController
{
    public $login_required = false;
    public $permission_required;
    public $prev_page = false;
    function index()
    {
        $this->title = 'Projetos';

        $model = $this->loadModel('projetos/ProjetosAdmModel');

        require ROOTPATH . '/public/views/includes/header.php';
        require ROOTPATH . '/public/views/includes/nav.php';

        require ROOTPATH . '/public/views/projetos/projetos-view.php';

        require ROOTPATH . '/public/views/includes/footer.php';
    }

    public function adm()
    {
        $this->title = 'Gerenciar Projetos';
        $permission_required = 'gerir-projetos';
        if (!$this->loggedIn) {
            $this->logout();
            $this->login();
            return;
        }

        if (!$this->check_permissions(
            $this->permission_required,
            $this->userdata['user_permissions']
        )) {
            echo 'no permission';
            return;
        }

        $modelo = $this->load_model('projetos/ProjetosAdmModel');

        require ROOTPATH . '/public/views/includes/header.php';
        require ROOTPATH . '/public/views/includes/nav.php';

        require ROOTPATH . '/public/views/projetos/projetos-view.php';

        require ROOTPATH . '/public/views/includes/footer.php';
    }

    function login() {}
}

