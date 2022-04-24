<?php

/**
 * A group of tools to manage users
 */

final class UsersManager
{
    private $premissionsManager;
    private $phpass;
    private $redirector;

    private static $userManager;

    private function __construct()
    {
        $this->premissionsManager = new PermissionsManager();
        $this->phpass = new PasswordHash(8, false);
        $this->redirector = new Redirect(HOME_URI, true);
    }

    public static function getTools()
    {
        if (!isset(self::$userManager))
            self::$userManager = new UsersManager();
        return self::$userManager;
    }

    public function getPremissionsManager()
    {
        return $this->premissionsManager;
    }

    public function getPhPass()
    {
        return $this->phpass;
    }

    public function getRedirect()
    {
        return $this->redirector;
    }
}
