<?php

/**
 * 
 */

class NewsController extends MainController
{
    public $news;
    public $date;
    public $use;

    public $userAssociations;

    public function __construct(
        $parameters = array(),
        $title = 'index',
        $permissions = PermissionsManager::P_VIEW_NEWS
    ) {
        parent::__construct($parameters, $title, $permissions);
        $this->news = new NewsList();
        $this->date = new DateTime();
        $this->use = 'Y';

        $this->userAssociations = new AssociationsList();
    }

    protected function indexMain()
    {
        if(empty($this->parameters))
            $this->model->getNews($this->parameters);
        else
            $this->model->getNewsByDate($this->parameters);

        require VIEWS_PATH . '/news/home.php';
    }

    public function article()
    {
        $this->loadModel('news/News');

        $this->model->getNewsByID($this->parameters[0]);
        
        require VIEWS_PATH . '/news/article.php';
    }

    public function create()
    {
        $this->user->isLoggedIn() && !$this->loginRequired
            OR $this->user instanceof Partner
            OR exit();

        $this->loadModel('home/Home');

        $this->model->getUserAssociations();

        $this->loadModel('home/News');

        require VIEWS_PATH . '/includes/head.php';
        require VIEWS_PATH . '/includes/nav.php';

        require VIEWS_PATH . '/news/createNews.php';

        require VIEWS_PATH . '/includes/footer.php';
    }
}

