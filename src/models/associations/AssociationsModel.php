<?php

/**
 * 
 */

class AssociationsModel extends MainModel
{
    public function search()
    {
        if (
            !UsersManager::getTools()->getPremissionsManager()->checkPermissions(
                UserSession::getUser()->getPermissions(),
                PermissionsManager::P_VIEW_ASSOCIATIONS,
                false
            )
        )
            return;

        $associations = $this->db->query(
            $this->db->createQuery("SELECT * FROM `associations`
            INNER JOIN `usersAssociations`
            ON `associations`.`id` = `usersAssociations`.`association`;"));

        if (!$associations)
            return;

        foreach ($associations->fetchAll(PDO::FETCH_ASSOC) as $association)
            $this->controller->associations->add($this->instanceAssociation($association));
    }

    private function instanceAssociation(array $association)
    {
        $user = clone UserSession::getUser();
        $president = $user->getID() == $association['user'] ? $user : $this->instanceUserByID($association['user']);
        return $president->initAssociation(
            $association['id'],
            $association['name'],
            $association['nickname'],
            $association['address'],
            $association['telephone'],
            $association['taxpayerNumber']
        );
    }

    private function instanceUserByID(int $id)
    {
        $extends = 'User';
        try {
            $user = $this->db->query(
                $this->db->createQuery('SELECT * FROM `users` WHERE `id` = ?;'),
                [$id]
            );

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

            return new $extends(
                $user['username'],
                null,
                $user['realName'],
                $user['email'],
                $user['telephone'],
                $user['wallet'] ?? 0,
                $user['permissions'],
                false,
                $id
            );
        } catch (Exception $e) {
            die($e);
        }
    }

    public function createAssociation()
    {
        if (
            !UsersManager::getTools()->getPremissionsManager()->checkPermissions(
                UserSession::getUser()->getPermissions(),
                PermissionsManager::P_CREATE_ASSOCIATIONS,
                false
            )
        )
            return;

        $association = $_POST['create'];

        unset($_POST['create']);

        if (
            $this->db->query(
                $this->db->createQuery('SELECT * FROM `associations` WHERE `nickname` = ?;'),
                [
                    $association['nickname'],
                ]
            )->fetchAll()
        )
            return;

        if (!preg_match('/^[A-Z_]{8,}$/i', $association['nickname']))
            return;

        if (!$association['address'])
            unset($association['address']);

        if ($association['phone'] == 'yours' && null === UserSession::getUser()->getTelephone())
            return;

        if ($association['phone'] == 'new' && !isset($association['int'], $association['number']))
            return;

        $association['telephone'] = $association['phone'] == 'new'
            ? '+' . $association['int'] . ' ' . $association['number']
            : UserSession::getUser()->getTelephone();

        unset($association['phone'], $association['int'], $association['number']);

        $association = UserSession::getUser()->createAssociation(
            $association['name'],
            $association['nickname'],
            $association['address'],
            $association['telephone'],
            $association['taxpayerNumber'],
        );
    }

    public function enterAssocition($id)
    {
        $association = $this->db->query("SELECT * FROM `associations` WHERE `id` = $id;")->fetch(PDO::FETCH_ASSOC);

        if (!$association)
            return;

        $association = $this->instanceAssociation($association);

        $association->createPartner(UserSession::getUser());
    }
}
