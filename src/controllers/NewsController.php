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

    function adm()
    {
        $this->title = 'News - Administration';

        $parameters = func_num_args() >= 1 ? func_get_arg(0) : array();
        
        require ROOT_PATH . '/public/views/includes/header.php';
        require ROOT_PATH . '/public/views/includes/nav.php';

        require ROOT_PATH . '/public/views/news/home.php';

        require ROOT_PATH . '/public/views/includes/footer.php';
    }
}

