<?php

/**
 * 
 */

class HomeController extends MainController
{
    protected function indexMain()
    {
        require VIEWS_PATH . '/home/home.php';
    }
}

