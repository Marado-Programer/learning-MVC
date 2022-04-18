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

    public function joinPartnerToEvent(int $idEvent, Partner $user)
    {
        $event = $this->getEventByID($idEvent);

        $user->enterEvent($event);
    }

    private function instanceEvent(array $event)
    {
        $association = $this->db->query("SELECT * FROM `associations` WHERE `id` = " . $event['association'] . ";")->fetch(PDO::FETCH_ASSOC);

        if (!$association)
            return;
        
        $partner = $this->getPartnerByID($association['president']);
        
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
        
        $db = new SystemDB();

        if (!$db->pdo)
            die('Connection error');

        $db->pdo->beginTransaction();

        $userRoles = $db->query("SELECT `role` FROM `usersAssociations` WHERE `userID` = $id;")->fetchAll(PDO::FETCH_ASSOC);

        if (!$userRoles) {
            $db->pdo->rollBack();
            die('Internal error');
        }

        $db->pdo->commit();

        if (count($userRoles) > 0)
            $isPresident = false;
            foreach ($userRoles as $role) 
                if (UsersManager::getPermissionsManager()->checkPermissions(
                    $role['role'],
                    PermissionsManager::AP_PRESIDENT,
                    false
                ))
                    $isPresident = true;

        if (!$user)
            return;

        return $this->instancePartnerByID($user->fetch(PDO::FETCH_ASSOC), $isPresident);
    }

    private function instancePartnerByID(array $user, $isPresident)
    {
        $class = $isPresident ? 'President' : 'Partner';
        return new $class(
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

