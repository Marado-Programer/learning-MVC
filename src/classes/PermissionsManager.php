<?php

/**
 * Manage permissions, add and delete permissions from a user
 */

final class PermissionsManager
{
    /**
     * Normal Users premissions
     */

    public const P_ZERO = 0x0;
    
    public const P_VIEW_ASSOCIATIONS = 0x1;
    public const P_CREATE_ASSOCIATIONS = 0x1 << 1;
    public const P_DELETE_ASSOCIATIONS = 0x1 << 2;

    public const P_VIEW_NEWS = 0x1 << 3;
    public const P_DELETE_NEWS = 0x1 << 4;

    public const P_VIEW_EVENTS = 0x1 << 5;
    public const P_DELETE_EVENTS = 0x1 << 6;

    public const P_ADMNI_ASSOCIATIONS = self::P_VIEW_ASSOCIATIONS
        | self::P_CREATE_ASSOCIATIONS
        | self::P_DELETE_ASSOCIATIONS;
    public const P_ADMNI_NEWS = self::P_VIEW_NEWS
        | self::P_DELETE_NEWS;
    
    public const P_ADMNI_ALL = self::P_ADMNI_ASSOCIATIONS
        | self::P_ADMNI_NEWS;

    /**
     * Partners premissions
     */

    public const AP_PARTNER = 0x1;
    public const AP_PARTNER_ADMNI = 0x1 << 1;

    public const AP_CREATE_NEWS = 0x1 << 2;
    public const AP_EDIT_NEWS = 0x1 << 3;
    public const AP_DELETE_NEWS = 0x1 << 4;

    public const AP_CREATE_EVENTS = 0x1 << 5;
    public const AP_ENTER_EVENTS = 0x1 << 6;
    public const AP_EDIT_EVENTS = 0x1 << 7;
    public const AP_DELETE_EVENTS = 0x1 << 8;

    public const AP_ADMNI_NEWS = self::AP_CREATE_NEWS
        | self::AP_EDIT_NEWS
        | self::AP_DELETE_NEWS;

    public const AP_ADMNI_EVENTS = self::AP_CREATE_EVENTS
        | self::AP_ENTER_EVENTS
        | self::AP_EDIT_EVENTS
        | self::AP_DELETE_EVENTS;

    public const AP_PRESIDENT = self::AP_PARTNER
        | self::AP_PARTNER_ADMNI
        | self::AP_ADMNI_NEWS
        | self::AP_ADMNI_EVENTS;

    public function checkPermissions(
        int $userPermissions = self::P_ZERO,
        int $requiredPermissions,
        bool $strict = true
    ): bool {
        return (bool) (
            $strict
            ? $userPermissions == $requiredPermissions
            : $userPermissions & $requiredPermissions
        );
    }

    public function checkUserPermissions(
        User &$user = null,
        int $requiredPermissions,
        bool $strict = true
    ): bool {
        return (bool) (
            $strict
            ? $user->permissions == $requiredPermissions
            : $user->permissions & $requiredPermissions
        );
    }

    public function addUserPermissions(User &$user = null)
    {
        if (!isset($user))
            return;

        $permissions = func_get_args();
        $p = $user->getPermissions();

        for ($i = 1; $i < count($permissions); $i++)
            $p |= $permissions[$i];

        $user->setPermissions($p);
    }
    
    public function removeUserPermissions(User &$user = null)
    {
        if (!isset($user))
            return;

        $permissions = func_get_args();
        $p = $user->getPermissions();

        for ($i = 1; $i < count($permissions); $i++)
            $p &= ~$permissions[$i];

        $user->setPermissions($p);
    }
}
