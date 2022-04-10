<?php

/**
 * 
 */

class UserRegisterController extends MainController
{
    function indexMain()
    {
        require ROOT_PATH . '/public/views/user-register/signin.php'; 

        $this->model = $this->loadModel('user-register/SignInModel');

        if (isset($_POST['create']))
            $this->model->createUser();
    }
}

