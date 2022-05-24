<?php

/**
 *
 */

class Instanceator
{
    private static $instance;
    private $db;

    private function __construct(DBConnection $db)
    {
        $this->db = $db;
    }

    public static function getInstanceator(DBConnection $db)
    {
        if (!isset(self::$instance))
            self::$instance = new Instanceator($db);

        return self::$instance;
    }

    public function instanceUserByID(int $id)
    {
        $extends = 'User';
        try {
            $userQuery = $this->db->createQuery('SELECT * FROM `users` WHERE `id` = ?;');
            $data = [$id];
            if (UserSession::getUser()->getID() == $id)
                $user = UserSession::getUser();
            else
                $user = $this->db->query($userQuery, $data);

            if (!$user instanceof User) {
                if (!$user)
                    throw new Exception('Error fiding user');

                $user = $user->fetch(PDO::FETCH_ASSOC);

                $userRoles = $this->db->query(
                    $this->db->createQuery("SELECT `role` FROM `usersAssociations` WHERE `user` = ?;"),
                    [$user['id']]
                )->fetchAll(PDO::FETCH_ASSOC);

                if (count($userRoles) > 0) {
                    $extends = 'Partner';
                    foreach ($userRoles as $role) 
                        if (
                            UsersManager::getTools()->getPremissionsManager()->checkPermissions(
                                $role['role'],
                                PermissionsManager::AP_PRESIDENT,
                                false
                            )
                        ) {
                            $extends = 'President';
                            break;
                        }
                }

                $user = new $extends(
                    $id,
                    $user['username'],
                    null,
                    $user['realName'],
                    $user['email'],
                    $user['telephone'],
                    $user['wallet'] ?? 0,
                    $user['permissions'],
                    false
                );

                $this->db->resultToCache($userQuery, $data, $user, true);
            }

            return $user;
        } catch (Exception $e) {
            die($e);
        }
    }

    public function instanceAssociationByID(int $id)
    {
        try {
            $query = $this->db->createQuery('SELECT * FROM `associationWPresident` WHERE `id` = ?;');
            $data = [$id];
            $association = $this->db->query($query, $data);

            if (!$association instanceof Association) {
                if (!$association)
                    throw new Exception('Error fiding user');

                $association = $association->fetch(PDO::FETCH_ASSOC);

                $president = $this->instanceUserByID($association['president']);

                $association = new Association(
                    $association['id'],
                    $association['name'],
                    $association['nickname'],
                    $association['address'],
                    $association['telephone'],
                    $association['taxpayerNumber'],
                    $president,
                    $association['quotaPrice'],
                    $association['timeSpanToPayQuota'],
                    $association['payQuotaAtEntering']
                );
            }

            $this->db->resultToCache($query, $data, $association, true);

            return $association;
        } catch (Exception $e) {
            die($e);
        }
    }

    public function instanceAssociationByNickname(string $nickname)
    {
        try {
            $query = $this->db->createQuery('SELECT `id` FROM `associations` WHERE `nickname` = ?;');
            $data = [$nickname];
            $association = $this->db->query($query, $data);

            if (!$association instanceof Association) {
                if (!$association)
                    throw new Exception('Error fiding user');

                $association = $association->fetch(PDO::FETCH_ASSOC);

                $association = $this->instanceAssociationByID($association['id']);
            }

            $this->db->resultToCache($query, $data, $association, true);

            return $association;
        } catch (Exception $e) {
            die($e);
        }
    }

    public function instanceNewsByID(int $id)
    {
        try {
            $query = $this->db->createQuery('SELECT * FROM `news` WHERE `id` = ?;');
            $data = [$id];
            $news = $this->db->query($query, $data);

            if (!$news instanceof News) {
                if (!$news)
                    throw new Exception('Error fiding user');

                $news = $news->fetch(PDO::FETCH_ASSOC);

                $author = $this->instanceUserByID($news['author']);
                //$author->getMyNews();
                $association = $this->instanceAssociationByID($news['association']);

                $news = new News(
                    $association,
                    $author,
                    $news['title'],
                    $news['image'],
                    $news['publishTime'] ? DateTime::createFromFormat('Y-m-d H:i:s', $news['publishTime']) : null,
                    DateTime::createFromFormat('Y-m-d H:i:s', $news['lastEditTime']),
                    $news['id']
                );
            }

            $this->db->resultToCache($query, $data, $news, true);

            return $news;
        } catch (Exception $e) {
            die($e);
        }
    }

    public function instanceEventByID(int $id)
    {
        try {
            $query = $this->db->createQuery('SELECT * FROM `events` WHERE `id` = ?;');
            $data = [$id];

            $event = $this->db->query($query, $data);

            if (!$event instanceof Events) {

                $event = $event->fetch(PDO::FETCH_ASSOC);

                if (!$event)
                    throw new Exception('Error fiding user');

                $association = $this->instanceAssociationByID($event['association']);

                $event = new Events(
                    $association,
                    $event['title'],
                    $event['description'],
                    DateTime::createFromFormat('Y-m-d H:i:s', $event['endDate']),
                    $id
                );
            }

            $this->db->resultToCache($query, $data, $event, true);

            return $event;
        } catch (Exception $e) {
            die($e);
        }
    }

    public function instanceRegistrationsByPartnerID(int $id)
    {
        try {
            $partner = $this->instanceUserByID($id);

            $query = $this->db->createQuery('SELECT * FROM `registrations` WHERE `partner` = ?');
            $data = [$partner->getID()];

            $registrationsList = $this->db->query($query, $data)->fetchAll(PDO::FETCH_ASSOC);
            if (!$registrationsList)
                return;

            foreach ($registrationsList as $registration) {
                $event = $this->instanceEventByID($registration['event']);

                $registrations[] = new Registration(
                    $event,
                    $partner
                );
            }

            return $registrations;
        } catch (Exception $e) {
            die($e);
        }
    }
}

