<?php

/**
 * Manipula os dados de user registado, faz login e logout, verifica permissões
 * e redireciona página para user ativo.
 */

class UserLogin
{
    public $loggedIn;
    public $userData;
    public $loginErrorMessage;
    public $username;

    public function checkUserLogin()
    {
        if (
            isset($_SESSION['userData'])
            && !empty($_SESSION['userData'])
            && is_array($_SESSION['userData'])
            && !isset($_POST['user-data'])
        ) {
            $userData = $_SESSION['userData'];

            $userData['post'] = false;
        }

        if (
            isset($_POST['user-data'])
            && !empty($_POST['user-data'])
            && is_array($_POST['user-data'])
        ) {
            $userData = $_POST['user-data'];

            $userData['post'] = true;
        }

        if (!isset($userData) || !is_array($userData)) {
            $this->logout();

            return;
        }

        $post = $userData['post'] === true;

        unset($userData['post']);

        if (empty($userData)) {
            $this->loggedIn = false;
            $this->loginError = null;

            $this->logout();

            return;
        }

        extract($userdata);

        if (!isset($user) || !isset($userPassword)) {
            $this->loggedIn = false;
            $this->loginError = null;

            $this->logout();

            return;
        }

        $query = $this->db->query(
            'SELECT * FROM `users` WHERE `user` = ? LIMIT 1',
            array($user)
        );

        if (!$query) {
            $this->loggedIn = false;
            $this->loginError = 'Internal error.';

            $this->logout();

            return;
        }

        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        $userId = (int) $fetch['userId'];
        $userName = $fetch['username'];

        if (empty($userId)) {
            $this->loggedIn = false;
            $this->loginError = 'User do not exists.';

            $this->logout();

            return;
        }

        if ($this->phpass->CheckPassword($userPassword, $fetch['userPassword'])) {
            if (session_id() != $fetch['userSessionId'] && !$post) {
                $this->loggedIn = false;
                $this->loginError = 'Wrong session ID.';

                $this->logout();

                return;
            }

            if ($post) {
                session_regenerate_id();
                $sessionId = session_id();

                $_SESSION['userData'] = $fetch;

                $_SESSION['userData']['userPassword'] = $userPassword;              /* improve */

                $_SESSION['userData']['userSessionId'] = $sessionId;

                $query = $this->db->query(
                    'UPDATE `users` SET `userSessionId` = ? WHERE `userId` = ?;',
                    array($sessionId, $userId)
                );
            }

            $_SESSION['userData']['userPermissions'] = unserialize($fetch['userPermissions']);

            $this->loggedIn = true;
            $this->userName = $userName;

            $this->userData = $_SESSION['userData'];

            if (isset($_SESSION['gotoURL'])) {              /* improve */
                $gotoUrl = urldecode($_SESSION['gotoURL']); //
                                                            //
                unset($_SESSION['gotoURL']);                //
                                                            //
                $this->refresh($gotoURL);                   //
            }

            return;
        } else {
            $this->loggedIn = false;

            $this->loginError = 'Password does not match.';

            $this->logout();

            return;
        }
    }

    protected function logout($redirect = false)
    {
        $_SESSION['userData'] = array();                /* improve */
        unset($_SESSION['userData']);               /* improve */

        session_regenerate_id();

        if ($redirect === true)
            $this->gotoLogin();
    }

    protected function gotoLogin()
    {
        if (defined('ROOT_PATH')) {
            $login_uri = ROOT_PATH . '/login';

            $_SESSION['gotoURL'] = urlencode($_SERVER['REQUEST_URI']);
            
            $this->refresh($loginURI);
        }
        return;
    }
	
    private function refresh($page = null)              /* improve */
    {
        echo '<meta http-equiv="Refresh" content="0; url=' . $page . '" />';
        echo '<script type="text/javascript">window.location.href = "' . $page . '";</script>';
        header('location: '. $page);
	}

    final protected function gotoPage($pageURI = null)              /* improve */
    {
        if (isset($_GET['url']) && !empty($_GET['url']) && !$pageURI)
            $pageURI = urldecode($_GET['url']);

        if ($pageURI) {
            $this->refresh($pageURI);

			return;
        }
    }

    final protected function checkPermissions(
        $required = 'any',
        $userPermissions = array('any')
    ) {
        if (!is_array($userPermissions))
            return;

        return in_array($required, $userPermissions);
    }
}
