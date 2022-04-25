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
            $this->controller->events->add($this->instanceEvent($event));
    }
    
    public function getEventByID(int $id)
    {
        $events = $this->db->query(
            "SELECT * FROM `events`
            WHERE `id` = $id;"
        );

        if (!$events)
            return;

        $this->controller->events->add($event = $this->instanceEvent($events->fetch(PDO::FETCH_ASSOC)));

        return $event;
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
            $this->controller->events->add($this->instanceEvent($event));
    }
    
    public function joinAssociationToEvent(int $idEvent, int $idAssociation)
    {
        $event = $this->getEventByID($idEvent);
        $event->addAssociation($this->instancer->instanceAssociationByID($idAssociation));
    }

    public function joinPartnerToEvent(int $idEvent, Partner $user)
    {
        $event = $this->getEventByID($idEvent);

        $user->enterEvent($event);
    }

    private function instanceEvent(array $event)
    {
        $association = $this->db->query("SELECT * FROM `associationWPresident` WHERE `id` = " . $event['association'] . ";")->fetch(PDO::FETCH_ASSOC);

        if (!$association)
            return;
        
        $partner = $this->instancer->instanceUserByID($association['president']);
        
        $association = $partner->initAssociation(
            $association['id'],
            $association['name'],
            $association['nickname'],
            $association['address'],
            $association['telephone'],
            $association['taxpayerNumber'],
        );

        return $association->initEvent(
            $event['title'],
            $event['description'],
            DateTime::createFromFormat('Y-m-d H:i:s', $event['endDate']),
            $event['id']
        );
    }
}

