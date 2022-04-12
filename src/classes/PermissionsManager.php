<?php

/**
 * Manage permissions, add and delete permissions from a user
 */

final class PermissionsManager
{
    public const P_ZERO = 0x0;
    
    public const P_VIEW_ASSOCIATIONS = 0x1;
    public const P_CREATE_ASSOCIATIONS = 0x1 << 1;
    public const P_EDIT_ASSOCIATIONS = 0x1 << 2;
    public const P_DELETE_ASSOCIATIONS = 0x1 << 3;
    
    public const P_ADMIN_ASSOCIATIONS = self::P_VIEW_ASSOCIATIONS
        | self::P_CREATE_ASSOCIATIONS
        | self::P_EDIT_ASSOCIATIONS
        | self::P_DELETE_ASSOCIATIONS;
    
    public const P_ALL = self::P_ADMIN_ASSOCIATIONS;

    public function checkPermissions(
        int $userPermissions = self::P_ZERO,
        int $requiredPermissions = self::P_ALL,
        bool $strict = true
    ) {
        return (bool) ($strict
            ? $userPermissions == $requiredPermissions
            : $userPermissions & $requiredPermissions);
    }

    public function checkUserPermissions(
        User &$user = null,
        int $requiredPermissions = self::P_ALL,
        bool $strict = true
    ) {
        return (bool) (
            $strict
            ? $user->permissions == $requiredPermissions
            : $user->permissions & $requiredPermissions
        );
    }

    public function addPermissions(User &$user = null)
    {
        if (!isset($user))
            return;

        $permissions = func_get_args();
        $p = $user->getPermissions();

        for ($i = 1; $i < count($permissions); $i++)
            $p |= $permissions[$i];

        $user->setPermissions($p);
    }
    
    public function removePermissions(&$user = null)
    {
        if (!isset($user) || get_class($user) != 'User')
            return;

        $permissions = func_get_args();
        $p = $user->getPermissions();

        for ($i = 1; $i < count($permissions); $i++)
            $p &= ~$permissions[$i];

        $user->setPermissions($p);
    }
}
