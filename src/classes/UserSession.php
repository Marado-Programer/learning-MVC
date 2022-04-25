<?php

/**
 * makes login and logout
 * and redirect to a certian page
 */

class UserSession extends Redirect
{
    private static $user;
    public $loginErrorMessage;
    public $goodLogIn = false;
    private $db;
    private $phpass;
    private $usingPost;

    public function __construct($db) {
        parent::__construct(HOME_URI, true);
        $this->db = $db;
        $this->phpass = UsersManager::getTools()->getPhPass();
        $this->checkUserLogin();
    }

    public function checkUserLogin()
    {
        $this->setUser();

        $extends = "User";

        // we need an username and a password to login
        if (!isset(self::$user->username) || null === self::$user->getPassword()) {
            $this->loginError();

            return;
        }

        $query = $this->db->query(
            $this->db->createQuery('SELECT * FROM `users` WHERE `users`.`username` = ? LIMIT 1'),
            array(self::$user->username)
        );

        // non-existent username
        if (!$query) {
            $this->loginError('Internal Error');

            return;
        }

        $fetchedUser = $query->fetch(PDO::FETCH_ASSOC);

        if (empty($fetchedUser)) {
            $this->loginError('User do not exists.');

            return;
        }

        $userId = (int) $fetchedUser['id'];
        $userUsername = $fetchedUser['username'];

        if (empty($userId)) {
            $this->loginError('User do not exists.');

            return;
        }

        // Right password for the user
        if ($this->phpass->CheckPassword(self::$user->getPassword(), $fetchedUser['password'])) {
            /**
             * You can only be logged in 1 browser a time.
             * So if the session id it's diffrent from what's in the DB
             * And you are not logging in using the POST you can't log in
             */
            if (session_id() != $fetchedUser['sessionID'] && !$this->usingPost) {
                $this->loginError('Wrong session ID.');

                return;
            }
            
            // If doing log in using POST (a form in HTML)
            if ($this->usingPost) {
                session_regenerate_id();
                $sessionId = session_id();

                $_SESSION['sessionId'] = $sessionId;

                // New session ID so now you can only log in using it
                $query = $this->db->update(
                    'users',
                    [
                        'id' => $userId
                    ],
                    [
                        'sessionID' => $sessionId
                    ]
                );
            }

            $userRoles = $this->db->query(
                $this->db->createQuery("SELECT `role` FROM `usersAssociations` WHERE `user` = ?;"),
                    [
                        $userId
                    ]
                )->fetchAll(PDO::FETCH_ASSOC);

            if (count($userRoles) > 0) {
                $extends = "Partner";
                foreach ($userRoles as $role) 
                    if (
                        UsersManager::getTools()->getPremissionsManager()->checkPermissions(
                            $role['role'],
                            PermissionsManager::AP_PRESIDENT,
                            false
                        )
                    ) {
                        $extends = 'President';
                        break;
                    }
            }

            self::$user = new $extends(
                $userId,
                $userUsername,
                self::$user->getPassword(),
                $fetchedUser['realName'],
                $fetchedUser['email'],
                $fetchedUser['telephone'],
                $fetchedUser['wallet'] ?? 0,
                $fetchedUser['permissions'],
                true
            );

            $_SESSION['user'] = serialize(self::$user);

            if (isset($_SESSION['gotoURL'])) {
                $gotoURL = urldecode($_SESSION['gotoURL']);
                unset($_SESSION['gotoURL']);
                $this->redirect($gotoURL);
            }

            $this->goodLogIn = true;

            return;
        }

        $this->loginError('Password does not match.');

        return;
    }

    private function setUser()
    {
        if (isset($_SESSION['user']))
            self::$user = unserialize($_SESSION['user']);

        /**
         * Verify if already exists a user logged in.
         * If there isn't, we see if someone it's trying to login using a form
         */
        
        if (
            isset($_POST['user-data'])
            && !empty($_POST['user-data'])
            && is_array($_POST['user-data'])
        ) {
            self::$user = new User(
                null,
                $_POST['user-data']['username'],
                $_POST['user-data']['password']
            );
        
            $this->usingPost = true;

            return;
        }

        if (isset(self::$user) && self::$user->isLoggedIn() === true) {
            $this->usingPost = false;

            return;
        }        

        self::$user = new User();

        $this->usingPost = false;
    }

    public static function getUser()
    {
        return self::$user ?? new User();
    }

    // Steps to do if an error occurs while loging in
    private function loginError($message = null)
    {
            $_SESSION['user'] = serialize(self::$user);
            $this->loggedIn = false;
            $this->loginErrorMessage = $message;

            $this->logout();

            return;
    }

    public function logout($redirect = false)
    {
        // remove all user data
        unset($_SESSION['user']);
        self::$user = new User();

        // new session id for new log in
        session_regenerate_id();

        if ($redirect)
            $this->gotoLogin();
    }

    // redirects you to the login page
    public function gotoLogin()
    {
        if (defined('HOME_URI')) {
            $loginURI = HOME_URI . '/login';
            
            $this->redirect($loginURI);
        }
        return;
    }
}
