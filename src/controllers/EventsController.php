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
        if (isset($_POST['search-events']))
            $this->model->getEventsByAssociationID($_POST['search-events']);
        else
            $this->model->getEvents();

        if (isset($_POST['event'])) {
            $event = $_POST['event'];
            if (isset($_POST['asAssociation']))
                $this->model->joinAssociationToEvent($event['id'], $_POST['asAssociation']);
            elseif (isset($_POST['asPartner']))
                if (($user = UserSession::getUser()) instanceof Partner)
                    $this->model->joinPartnerToEvent($event['id'], $user);
        }

        require VIEWS_PATH . '/events/index.php';
    }

    public function simple()
    {
        $this->indexMain();
    }
}
