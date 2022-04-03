<?php

/**
 * 
 */

class HomeController extends MainController
{
    public function index()
    {
        $this->title = 'Home';

        $parameters = func_num_args() >= 1 ? func_get_arg(0) : array();
        
        require VIEWS_PATH . '/includes/header.php';
        require VIEWS_PATH . '/includes/nav.php';

        require VIEWS_PATH . '/home/home.php';

        require VIEWS_PATH . '/includes/footer.php';
    }
}

