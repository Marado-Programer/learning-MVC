<?php

/**
 * 
 */

class UserRegisterController extends MainController
{
    function indexMain()
    {
        $this->loadModel('user-register/SignUpModel');

        if (isset($_POST['create'])) {
            $this->model->createUser();
            unset($_POST['create']);
        }

        require VIEWS_PATH . '/user-register/sign-up.php';
    }
}

