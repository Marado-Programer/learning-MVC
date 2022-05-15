<?php

/**
 * 
 */

class WalletModel extends MainModel
{
    public function deposit($params)
    {
        $user = $this->instancer->instanceUserByID($params['user']);
        $user->deposit($params['quantity']);
        UsersManager::getTools()->getRedirect()->redirect($_SERVER['HTTP_REFERER']);
    }
}
