<?php

/**
 * 
 */

class HomeController extends MainController
{
    function index()
    {
        $this->title = 'Home';

        $parameters = func_num_args() >= 1 ? func_get_arg(0) : array();
        
        require ROOTPATH . '/public/views/includes/header.php';
        require ROOTPATH . '/public/views/includes/nav.php';

        require ROOTPATH . '/public/views/home/home.php';

        require ROOTPATH . '/public/views/includes/footer.php';
    }
}

