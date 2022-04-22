<?php

/**
 * Manage permissions, add and delete permissions from a user
 */

final class PermissionsManager
{
    public const P_ZERO = 0x0;

    /**
     * Users premissions
     */
    
    public const P_VIEW_ASSOCIATIONS = 0x1;
    public const P_ENTER_ASSOCIATIONS = 0x1 << 1;
    public const P_CREATE_ASSOCIATIONS = 0x1 << 2;
    public const P_DELETE_ASSOCIATIONS = 0x1 << 3;

    public const P_VIEW_NEWS = 0x1 << 4;
    public const P_DELETE_NEWS = 0x1 << 5;

    public const P_VIEW_EVENTS = 0x1 << 6;
    public const P_DELETE_EVENTS = 0x1 << 7;

    public const P_ADMNI_ASSOCIATIONS = self::P_VIEW_ASSOCIATIONS
        | self::P_ENTER_ASSOCIATIONS
        | self::P_CREATE_ASSOCIATIONS
        | self::P_DELETE_ASSOCIATIONS;

    public const P_ADMNI_NEWS = self::P_VIEW_NEWS
        | self::P_DELETE_NEWS;
    
    public const P_ADMNI_EVENTS = self::P_VIEW_EVENTS
        | self::P_DELETE_EVENTS;
    
    public const P_ADMNI_ALL = self::P_ADMNI_ASSOCIATIONS
        | self::P_ADMNI_NEWS;

    public const P_CREATED_USER = self::P_VIEW_ASSOCIATIONS
        | self::P_ENTER_ASSOCIATIONS
        | self::P_CREATE_ASSOCIATIONS
        | self::P_VIEW_NEWS;

    /**
     * Partners premissions
     */

    public const AP_PARTNER = 0x1;
    public const AP_PARTNER_ADMNI = 0x1 << 1;

    public const AP_CREATE_NEWS = 0x1 << 2;
    public const AP_PUBLISH_NEWS = 0x1 << 3;
    public const AP_EDIT_NEWS = 0x1 << 4;
    public const AP_DELETE_NEWS = 0x1 << 5;

    public const AP_CREATE_EVENTS = 0x1 << 6;
    public const AP_ENTER_EVENTS = 0x1 << 7;
    public const AP_EDIT_EVENTS = 0x1 << 8;
    public const AP_DELETE_EVENTS = 0x1 << 9;

    public const AP_CREATE_IMAGES = 0x1 << 10;
    public const AP_EDIT_IMAGES = 0x1 << 11;
    public const AP_DELETE_IMAGES = 0x1 << 12;

    public const AP_ADMNI_NEWS = self::AP_CREATE_NEWS
        | self::AP_PUBLISH_NEWS
        | self::AP_EDIT_NEWS
        | self::AP_DELETE_NEWS;

    public const AP_ADMNI_EVENTS = self::AP_CREATE_EVENTS
        | self::AP_ENTER_EVENTS
        | self::AP_EDIT_EVENTS
        | self::AP_DELETE_EVENTS;

    public const AP_ADMNI_IMAGES = self::AP_CREATE_IMAGES
        | self::AP_EDIT_IMAGES
        | self::AP_DELETE_IMAGES;

    public const AP_PRESIDENT = self::AP_PARTNER
        | self::AP_PARTNER_ADMNI
        | self::AP_ADMNI_NEWS
        | self::AP_ADMNI_EVENTS
        | self::AP_ADMNI_IMAGES;

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
        User $user = null,
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
