<?php

/**
 * Manage permissions, add and delete permissions from a user
 */

final class PermissionsManager
{
    public const P_ZERO = 0x0;
    
    public const P_VIEW_PROJECTS = 0x1;
    public const P_CREATE_PROJECTS = 0x1 << 1;
    public const P_EDIT_PROJECTS = 0x1 << 2;
    public const P_DELETE_PROJECTS = 0x1 << 3;
    
    public const P_ADMIN_PROJECTS = self::P_VIEW_PROJECTS
        | self::P_CREATE_PROJECTS
        | self::P_EDIT_PROJECTS
        | self::P_DELETE_PROJECTS;
    
    public const P_ALL = self::P_ADMIN_PROJECTS;

    public function checkPermissions(
        $userPermissions = self::P_ZERO,
        $requiredPermissions = self::P_ALL,
        $strict = true
    ) {
        if (!is_numeric($userPermissions))
            return;

        return (bool) ($strict
            ? $userPermissions == $requiredPermissions
            : $userPermissions & $requiredPermissions);
    }

    public function checkUserPermissions(
        &$user = null,
        $requiredPermissions = self::P_ALL,
        $strict = true
    ) {
        if (!isset($user) || get_class($user) != 'User')
            return;
        
        if (!is_numeric($requiredPermissions))
            return;

        return (bool) (
            $strict
            ? $user->getPermissions() == $requiredPermissions
            : $user->getPermissions() & $requiredPermissions
        );
    }

    public function addPermissions(&$user = null)
    {
        if (!isset($user) || get_class($user) != 'User')
            return;

        $permissions = func_get_args();

        for ($i = 1; $i < count($permissions); $i++)
            $user->setPermissions($user->getPermissions() | $permissions[$i]);
    }
    
    public function removePermissions(&$user = null)
    {
        if (!isset($user) || get_class($user) != 'User')
            return;

        $permissions = func_get_args();

        for ($i = 1; $i < count($permissions); $i++)
            $user->setPermissions($user->getPermissions() & ~$permissions[$i]);
    }
}
