<?php

/**
 * 
 */

class AssociationsModel extends MainModel
{
    public function search()
    {
        if (
            !UsersManager::getPermissionsManager()->checkUserPermissions(
                $this->controller->userSession->user,
                PermissionsManager::P_VIEW_ASSOCIATIONS,
                false
            )
        )
            return;

        $associations = $this->db->query('SELECT * FROM `associations`;');

        if (!$associations)
            return;

        foreach ($associations->fetchAll() as $association) {
            $this->controller->associations->add($this->instanceAssociation($association));
        }
    }

    private function instanceAssociation(array $association)
    {
        return new Association(
            $association['name'],
            $association['address'],
            $association['telephone'],
            $association['taxpayerNumber'],
            $this->instanceUserByID($association['president'])
        );
    }

    private function instanceUserByID(int $id)
    {
        $user = $this->db->query("SELECT * FROM `users` WHERE `id` = $id;");

        if (!$user)
            return;

        $user = $user->fetch(PDO::FETCH_ASSOC);

        return new User(
            $user['username'],
            null,
            $user['realName'],
            $user['email'],
            null,
            $user['permissions'],
            false,
            $id
        );
    }

    public function createAssociation()
    {
        if (
            !UsersManager::getPermissionsManager()->checkUserPermissions(
                $this->controller->userSession->user,
                PermissionsManager::P_CREATE_ASSOCIATIONS,
                false
            )
        )
            return;

        $association = $_POST['create'];

        unset($_POST['create']);

        if (
            $this->db->query(
                'SELECT * FROM `associations` WHERE `name` = ?;',
                [
                    $association['name'],
                ]
            )->fetchAll()
        )
            return;

        if (!$association['address'])
            unset($association['address']);

        if ($association['phone'] == 'yours' && !isset($this->controller->userSession->user->telephone))
            return;

        if ($association['phone'] == 'new' && !isset($association['int'], $association['number']))
            return;

        $association['telephone'] = $association['phone'] == 'new'
            ? '+' . $association['int'] . ' ' . $association['number']
            : $this->controller->userSession->user->telephone;

        unset($association['phone'], $association['int'], $association['number']);

        $this->db->insert('associations', array_merge(
            $association,
            [
                'president' => $this->controller->userSession->user->id
            ]
        ));
    }
}
