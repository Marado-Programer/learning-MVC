<?php

/**
 * 
 */

class WalletModel extends MainModel
{
    public function deposit($params)
    {
        $user = $this->instanceUserByID($params['user']);
        $user->deposit($params['quantity']);
        UsersManager::getTools()->getRedirect()->redirect($_SERVER['SERVER_REFERER']);
    }

    private function instanceUserByID(int $id)
    {
        if (($user = UserSession::getUser())->getID() == $id)
            return $user;

        $user = $this->db->query("SELECT * FROM `users` WHERE `id` = $id;");

        if (!$user)
            return;

        $user = $user->fetch(PDO::FETCH_ASSOC);

        return new User(
            $id,
            $user['username'],
            null,
            $user['realName'],
            $user['email'],
            $user['telephone'],
            $user['permissions'],
            false
        );
    }
}
