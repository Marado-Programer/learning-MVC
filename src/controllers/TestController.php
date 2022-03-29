<?php

/**
 * 
 */

class TestController extends MainController
{
    function index()
    {
        $this->title = 'test';

        $parameters = func_num_args() >= 1 ? func_get_arg(0) : array();
        
        require ROOTPATH . '/public/views/includes/header.php';
        require ROOTPATH . '/public/views/includes/nav.php';

        require ROOTPATH . '/public/views/includes/footer.php';
    }

    function testFunc()
    {
        echo func_get_arg(0)[0];
    }
}

