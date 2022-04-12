<?php

/**
 * 
 */

class NewsController extends MainController
{
    protected function indexMain()
    {
        require ROOT_PATH . '/public/views/news/home.php';
    }

    public function create()
    {
        require ROOT_PATH . '/public/views/includes/header.php';
        require ROOT_PATH . '/public/views/includes/nav.php';

        require ROOT_PATH . '/public/views/news/home.php';

        require ROOT_PATH . '/public/views/includes/footer.php';
    }
}

