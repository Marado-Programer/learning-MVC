<?php

/**
 * 
 */

class EventsModel extends MainModel
{
    public function getEvents()
    {
        $events = $this->db->query('SELECT * FROM `events`;');

        if (!$events)
            return;

        foreach ($events->fetchAll(PDO::FETCH_ASSOC) as $event)
            $this->controller->events->add($this->instancer->instanceEventByID($event['id']));
    }

    public function getEventsByAssociationID(int $id)
    {
        $events = $this->db->query(
            "SELECT * FROM `events`
            WHERE `id` NOT IN (
                SELECT `event` FROM `associationsEvents`
                WHERE `association` = $id
                GROUP BY `event`
            );"
        );

        if (!$events)
            return;

        foreach ($events->fetchAll(PDO::FETCH_ASSOC) as $event)
            $this->controller->events->add($this->instancer->instanceEventByID($event['id']));
    }
    
    public function joinAssociationToEvent(int $idEvent, int $idAssociation)
    {
        $event = $this->instancer->instanceEventByID($idEvent);

        $event->addAssociation($this->instancer->instanceAssociationByID($idAssociation));
    }

    public function joinPartnerToEvent(int $idEvent, Partner $user)
    {
        $event = $this->instancer->instanceEventByID($idEvent);

        $user->enterEvent($event);
    }
}

