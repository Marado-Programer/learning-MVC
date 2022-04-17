<?php

/**
 * 
 */

class EventsController extends MainController
{
    public $events;

    public function __construct(
        $parameters = array(),
        $title = 'index',
        $permissions = PermissionsManager::P_VIEW_EVENTS
    ) {
        parent::__construct($parameters, $title, $permissions);
        $this->events = new EventsList();
    }

    protected function indexMain()
    {
        $this->model = $this->loadModel('events/EventsModel');

        $this->model->getEvents($this->parameters);

        require VIEWS_PATH . '/events/index.php';
    }
}


