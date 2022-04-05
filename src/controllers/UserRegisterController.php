<?php

/**
 * 
 */

class UserRegisterController extends MainController
{
    function index()
    {
        $this->title = 'User Registation';

        $parameters = func_num_args() >= 1 ? func_get_arg(0) : array();
        
        require ROOT_PATH . '/public/views/includes/header.php';
        require ROOT_PATH . '/public/views/includes/nav.php';

        require ROOT_PATH . '/public/views/user-register/signin.php'; 

        require ROOT_PATH . '/public/views/includes/footer.php';
    }
}

