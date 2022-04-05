<?php

/**
 * Carrega a página "/views/login/index.php"
 */

class LoginController extends MainController
{
    public function index()
    {
        $this->title = 'Login';

        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();

        require VIEWS_PATH . '/includes/header.php';
        require VIEWS_PATH . '/includes/nav.php';

        require VIEWS_PATH . '/login/login.php';

        require VIEWS_PATH . '/includes/footer.php';
    }

    public function delete()
    {
        $this->logout();
        $this->gotoLogin();
        return;
    }

}
