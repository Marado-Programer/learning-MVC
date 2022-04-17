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

        if (isset($_POST['search-events']))
            $this->model->getEventsByAssociationID($_POST['search-events']);
        else
            $this->model->getEvents();

        if (isset($_POST['asAssociation']))
            $this->model->joinAssociationToEvent($_POST['event']['id'], $_POST['asAssociation']);
        elseif (isset($_POST['asPartner']))
            $this->model->joinPartnerToEvent();
        
        require VIEWS_PATH . '/events/index.php';
    }

    public function simple()
    {
        $this->indexMain();
    }
}
