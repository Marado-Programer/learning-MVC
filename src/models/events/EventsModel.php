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
                SELECT `eventID` FROM `associationsEvents`
                WHERE `associationID` = $id
                GROUP BY `eventID`
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
        $event->addAssociation($this->getAssociationByID($idAssociation));
    }

    private function instanceEvent(array $event)
    {
        return new Events(
            $this->getAssociationByID($event['association']),
            $event['title'],
            $event['description'],
            DateTime::createFromFormat('Y-m-d H:i:s', $event['endDate']),
            $event['id']
        );
    }

    private function getAssociationByID($id)
    {
        $association = $this->db->query("SELECT * FROM `associations` WHERE `id` = $id;");

        if (!$association)
            return;

        return $this->instanceAssociation($association->fetch(PDO::FETCH_ASSOC));
    }

    private function instanceAssociation(array $association)
    {
        return new Association(
            $association['id'],
            $association['name'],
            $association['nickname'],
            $association['address'],
            $association['telephone'],
            $association['taxpayerNumber'],
            $this->getPartnerByID($association['president'])
        );
    }

    private function getPartnerByID(int $id)
    {
        $user = $this->db->query("SELECT * FROM `users` WHERE `id` = $id;");

        if (!$user)
            return;

        return $this->instancePartnerByID($user->fetch(PDO::FETCH_ASSOC));
    }

    private function instancePartnerByID(array $user)
    {
        return new Partner(
            $user['username'],
            null,
            $user['realName'],
            $user['email'],
            $user['telephone'],
            $user['permissions'],
            false,
            $user['id']
        );
    }
}

