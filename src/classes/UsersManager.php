<?php

/**
 * A group of tools to manage users
 */

final class UsersManager
{
    public $permissionManager;
    public $phpass;
    public $redirector;

    private static $userManager;

    private function __construct()
    {
        $this->permissionManager = new PermissionsManager();
        $this->phpass = new PasswordHash(8, false);
        $this->redirector = new Redirect(HOME_URI, true);
    }

    public static function getTools()
    {
        if (!isset(self::$userManager))
            self::$userManager = new UsersManager();
        return self::$userManager;
    }
}
