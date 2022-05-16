<?php

/**
 * This model can register a new user to the system
 */

class UserRegisterModel extends MainModel
{
    public function createUser()
    {
        if (!checkArray($_POST['register'], 'username', 'email', 'password', 'verify-password')) {
            $_SESSION['sign-up-errors'][] = 'Not all the required inputs had value';
            $_SESSION['sign-up-values'] = [
                'username' => $_POST['register']['username'],
                'real-name' => $_POST['register']['realName'],
                'email' => $_POST['register']['email'],
                'int' => $_POST['register']['int'],
                'number' => $_POST['register']['number'],
            ];
            return;
        }

        $user = $_POST['register'];
        unset($_POST['register']);
        $user = array_map('trim', $user);

        // username needs to be between 4-32 chars and use only latin letters
        // arabic numbers and underscores
        if (!preg_match('/^[A-Z0-9_]{4,32}$/i', $user['username'])) {
            $_SESSION['sign-up-errors'][] = 'username has unsupported size (min 4 characters, max 32 characters)';
            $_SESSION['sign-up-errors'][] = 'username can only have letters, numbers and underscores';
            $user['username'] = substr($user['username'], 0, 32);
        }

        // user's real name needs to be less than 80 chars
        if (isset($user['realName']))
            if (strlen($user['realName']) > 80 && strlen($user['realName']) >= 0) {
                $_SESSION['sign-up-errors'][] = 'User\'s real name can\'t pass 80 bytes';
                $user['realName'] = substr($user['realName'], 0, 80);
            }

        // check if e-mail it's valid
        /**
         * regular expression from here:
         * https://emailregex.com/
         */
        $emailRegex = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';
        if (!preg_match($emailRegex, $user['email'])) {
            $_SESSION['sign-up-errors'][] = 'Invalid e-mail';
            $user['email'] = "";
        }

        // create phone number
        if(isset($user['int'], $user['number']))
            if (strlen($user['number']) > 15) {
                $_SESSION['sign-up-errors'][] = 'Invalid phone number size';
                $user['number'] = substr($user['number'], 0, 15);
            }
        $user['telephone'] = $user['number'] == '' ? '' : '+' . $user['int'] . ' ' . $user['number'];
        
        // password can't be too short. 4 chars it's short tho
        if (!preg_match('/(?=.*\w)(?=.*\d)(?=.*\W).{8,}/', $user['password'])) {
            $_SESSION['sign-up-errors'][] = 'Password i\'ts too short and not secure at all';
            $_SESSION['sign-up-errors'][] = 'I recomend you to create an password with uppercase, lowercase, numbers and special characters';
            $_SESSION['sign-up-errors'][] = 'It\'s also a good idea to use a password manager. My recomendations are: <a href="https://keepassxc.org">KeePassXC</a>, <a href="https://www.passwordstore.org">GNU pass</a> or <a href="https://keepass.info">KeePass2</a>';
        }

        if ($user['password'] !== $user['verify-password'])
            $_SESSION['sign-up-errors'][] = 'Passwords don\'t match';

        unset($user['verify-password']);

        if (isset($_SESSION['sign-up-errors']) && count($_SESSION['sign-up-errors']) > 0) {
            $_SESSION['sign-up-values'] = [
                'username' => $user['username'],
                'real-name' => $user['realName'],
                'email' => $user['email'],
                'int' => $user['int'],
                'number' => $user['number']
            ];
            return;
        }

        unset($user['int'], $user['number']);

        // check for equal data in the DB.
        $row = $this->db->query(
            'SELECT COUNT('
                . 'CASE WHEN `username` = ? THEN TRUE END'
            . ') AS `username`, COUNT('
                . 'CASE WHEN `email` = ? THEN TRUE END'
            . ') AS `email`' . ($user['telephone'] ? ', COUNT('
                . 'CASE WHEN `telephone` = ? THEN TRUE END'
            . ') AS `telephone` ' : ' ')
            . 'FROM `users`',
            $user['telephone']
                ? [
                    $user['username'],
                    $user['email'],
                    $user['telephone']
                ]
                : [
                    $user['username'],
                    $user['email']
                ]
        )->fetch(PDO::FETCH_ASSOC);

        if (checkArray($row, 'username'))
            $_SESSION['sign-up-errors'][] = "Invalid username, " . $user['username'] . " already in use";

        if (checkArray($row, 'email'))
            $_SESSION['sign-up-errors'][] = "Invalid email, " . $user['email'] . " already in use";

        if (checkArray($row, 'telephone') && $user['telephone'] )
            $_SESSION['sign-up-errors'][] = "Invalid telephone, " . $user['telephone'] . " already in use";

        if (isset($_SESSION['sign-up-errors']) && !empty($_SESSION['sign-up-errors']))
            return;

        $user['password'] = $this->controller->tools->getPhpass()->HashPassword(
            $user['password']
        );

        if (!$user['realName'])
            unset($user['realName']);

        if ($this->db->insert(
            'users',
            array_merge($user, [
                'permissions' => dechex(PermissionsManager::P_CREATED_USER)
            ])
        ))
            $_SESSION['sign-up-succeed'] = true;

        unset($_SESSION['sign-up-values'], $_SESSION['sign-up-errors']);

        $this->controller->tools->getRedirect()->redirect(HOME_URI . '/login');
    }
}
