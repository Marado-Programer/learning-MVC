<?php

/**
 * 
 */

class PayQuotaModel extends MainModel
{
    public function transfer($params)
    {
        $user = $this->instanceUserByID($params['user']);
        $association = $this->instanceAssociationByID($params['association']);

        foreach ($user->getQuotas() as $quota)
            if ($quota->association->getID() == $association->getID()) {
                $user->payQuota($quota, $params['quantity']);

                break;
            }
        UsersManager::getTools()->getRedirect()->redirect($_SERVER['HTTP_REFERER']);
    }

    private function instanceUserByID(int $id)
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
            }

            $this->db->resultToCache($userQuery, $data, $user, true);

            return $user;
        } catch (Exception $e) {
            die($e);
        }
    }

    private function instanceAssociationByID(int $id)
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

}
