<?php

/**
 * 
 */

class NewsController extends MainController
{
    public $news;
    public $date;
    public $use;

    public function __construct(
        $parameters = array(),
        $title = 'index',
        $permissions = PermissionsManager::P_VIEW_NEWS
    ) {
        parent::__construct($parameters, $title, $permissions);
        $this->news = new NewsList();
        $this->date = new DateTime();
        $this->use = 'Y';
    }

    protected function indexMain()
    {
        $this->model = $this->loadModel('news/NewsModel');

        $this->model->getNewsByDate($this->parameters);

        require VIEWS_PATH . '/news/home.php';
    }

    public function article()
    {
        $this->model = $this->loadModel('news/NewsModel');

        $this->model->getNewsByID($this->parameters[0]);
        
        require VIEWS_PATH . '/news/article.php';
    }
}

