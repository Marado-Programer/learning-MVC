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

        $model = $this->load_model('projetos/projetos-adm-model');

        require ROOTPATH . '/public/views/includes/header.php';
        require ROOTPATH . '/public/views/includes/nav.php';

        require ROOTPATH . '/public/views/projetos/projetos-view.php';

        require ROOTPATH . '/public/views/includes/footer.php';
    }
}

