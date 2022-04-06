<?php

/**
 * 
 */

class User
{
    public $loggedIn;
    public $id;
    public $username;
    public $password;
    public $realName;
    public $permissions;

    public function __construct()
    {
        $this->loggedIn = false;
        $this->permissions = PermissionsManager::P_ZERO;
    }
}
