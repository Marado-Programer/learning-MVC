<?php

/**
 *
 */

class SignInModel extends MainModel
{
    public function createUser()
    {
        $user = $_POST['register'];

        unset($_POST['register']);

        if (!preg_match('/^[A-Z]{4,32}$/i', $user['username']))
            return;

        if (strlen($user['realName']) > 80 && strlen($user['realName']) >= 0)
            return;

        $emailRegex = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';
        if (!preg_match($emailRegex, $user['email']))
            return;

        $user['telephone'] = '+' . $user['int'] . ' ' . $user['number'];

        unset($user['int'], $user['number']);
        
        if (strlen($user['password']) < 4)
            return;

        if ($user['password'] !== $user['verify-password'])
            return;

        unset($user['verify-password']);

        if (
            $this->db->query(
                'SELECT * FROM `users` WHERE (`username` = ?) OR (`email` = ?) OR (`telephone` = ?);',
                [
                    $user['username'],
                    $user['email'],
                    $user['telephone']
                ]
            )->fetchAll()
        )
            return;

        $phpass = new PasswordHash(8, false);

        $user['password'] = $phpass->HashPassword($user['password']);

        unset($phpass);

        if (!$user['realName'])
            unset($user['realName']);

        $this->db->insert('users', array_merge(
            $user,
            [
                'permissions' =>
                    PermissionsManager::P_VIEW_ASSOCIATIONS
                    | PermissionsManager::P_CREATE_ASSOCIATIONS
            ]
        ));
    }
}

