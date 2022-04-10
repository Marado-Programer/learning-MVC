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

        if ($user['password'] !== $user['verify-password'])
            return;

        unset($user['verify-password']);

        if (
            $this->db->query(
                'SELECT * FROM `users` WHERE (`username` = ?) OR (`email` = ?);',
                [
                    $user['username'],
                    $user['email']
                ]
            )->fetchAll()
        )
            return;

        $phpass = new PasswordHash(8, false);

        $user['password'] = $phpass->HashPassword($user['password']);

        unset($phpass);

        if (!$user['realName'])
            unset($user['realName']);

        $this->db->insert('users', $user);
    }
}

