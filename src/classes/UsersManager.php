<?php

/**
 * Manage the logged user data
 */

final class UsersManager
{
    public static $permissionManager;

    private function __construct()
    {

    }

    public static function getPermissionsManager()
    {
        if (!isset(self::$permissionManager))
            self::$permissionManager = new PermissionsManager();
        return self::$permissionManager;
    }
}