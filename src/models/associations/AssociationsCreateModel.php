<?php

/**
 * 
 */

class AssociationsCreateModel extends MainModel
{
    public function createAssociation()
    {
        if (
            !$this->controller->userSession->permissionManager->checkUserPermissions(
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
