<?php

/**
 * 
 */

class AssociationsModel extends MainModel
{
    public function search()
    {
        if (
            !UsersManager::getTools()->getPremissionsManager()->checkUserPermissions(
                $this->controller->user,
                PermissionsManager::P_VIEW_ASSOCIATIONS,
                false
            )
        )
            return;

        $associations = $this->db->query(
            $this->db->createQuery("SELECT * FROM `associations`")
        )->fetchAll(PDO::FETCH_ASSOC);

        if (!$associations)
            return;

        foreach ($associations as $association)
            $this->controller->associations->add($this->instancer->instanceAssociationByID($association['id']));
    }

    public function userPayQuota($userID, $assocID, $money)
    {
        $user = $this->instancer->instanceUserByID($userID);
        $association = $this->instancer->instanceAssociationByID($assocID);

        foreach ($user->getQuotas() as $quota)
            if ($quota->association->getID() == $association->getID()) {
                $user->payQuota($quota, $money);

                break;
            }
    }

    public function createAssociation()
    {
        if (
            !$this->controller->tools->getPremissionsManager()->checkUserPermissions(
                $this->controller->user,
                PermissionsManager::P_CREATE_ASSOCIATIONS,
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

        if ($association['phone'] == 'yours' && null === $this->controller->user->getTelephone())
            return;
        elseif ($association['phone'] == 'new' && !isset($association['int'], $association['number']))
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

        $this->controller->userSession->checkUserLogin();
    }

    public function enterAssocition($id)
    {
        $association = $this->instancer->instanceAssociationByID($id);

        $association->newPartner($this->controller->user);
    }
}
