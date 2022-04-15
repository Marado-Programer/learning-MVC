<?php

/**
 * 
 */

class NewsController extends MainController
{
    private $news;

    public function __construct(
        $parameters = array(),
        $title = 'index',
        $permissions = PermissionsManager::P_VIEW_NEWS
    ) {
        parent::__construct($parameters, $title, $permissions);
        $this->news = new NewsList();
    }
    protected function indexMain()
    {
        $this->model = $this->loadModel('news/NewsModel');

        $this->model->getNewsByDate($this->parameters);

        require VIEWS_PATH . '/news/home.php';
    }
}

