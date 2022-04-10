<?php

/**
 * Carrega a página "/views/login/index.php"
 */

class LoginController extends MainController
{
    protected function indexMain()
    {
        require VIEWS_PATH . '/login/login.php';
    }

    public function delete()
    {
        $this->userSession->logout(true);
        return;
    }

}
