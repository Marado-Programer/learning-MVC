<?php

/**
 * 
 */

class UserRegisterController extends MainController
{
    function indexMain()
    {
        require VIEWS_PATH . '/user-register/sign-up.php'; 

        $this->model = $this->loadModel('user-register/SignInModel');

        if (isset($_POST['create']))
            $this->model->createUser();
    }
}

