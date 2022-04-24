<?php

/**
 * Carrega a página "/views/login/index.php"
 */

class LoginController extends MainController
{
    protected function indexMain()
    {
        if (isset($_POST['log-in']) && $this->userSession->goodLogIn === true)
            UsersManager::getTools()->getRedirect()->redirect();

        require VIEWS_PATH . '/login/login.php';
    }

    public function delete()
    {
        $this->userSession->logout(true);
        return;
    }
}
