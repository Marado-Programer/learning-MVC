<?php

/**
 * 
 */

class AssociationsAdmniModel extends MainModel
{
    public function getAssociationByNickname($nickname)
    {
        $association = $this->db->query("SELECT * FROM `associations` WHERE `nickname` = '$nickname';");

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
            $user['telephone'],
            $user['permissions'],
            false,
            $id
        );
    }

    public function userAdmniPermissions(User $user, Association $association): int
    {
        $role = $this->db->query("SELECT * FROM `usersAssociations` WHERE (`associationID` = {$association->id}) AND (`userID` = {$user->id});");
        
        if (!$role)
            return 0;

        return $role->fetch(PDO::FETCH_ASSOC)['role'];
    }

    public function createNews()
    {
        $association = $this->db->query("SELECT * FROM `news`;");

        if (!$association)
            return;
    }
}
