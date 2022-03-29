<?php

/*
  Manipula os dados de user registado, faz login e logout, verifica permissões e
  redireciona página para user ativo.
 */

class UserLogin {
    public $logged_in;
    public $userdata;
    public $login_error;
    public $user_name;

    public function check_userlogin() {

        // Verifica se existe uma sessão com a chave userdata
        // Tem que ser um array e não pode ser HTTP POST
        if (isset($_SESSION['userdata']) && !empty($_SESSION['userdata']) && is_array($_SESSION['userdata']) && !isset($_POST['userdata'])
        ) {
            // Configura os dados do user
            $userdata = $_SESSION['userdata'];

            // Garante que não é HTTP POST
            $userdata['post'] = false;
        }

        // Verifica se existe um $_POST com a chave userdata
        // Tem que ser um array
        if (isset($_POST['userdata']) && !empty($_POST['userdata']) && is_array($_POST['userdata'])
        ) {
            // Configura os dados do user
            $userdata = $_POST['userdata'];

            // Garante que é HTTP POST
            $userdata['post'] = true;
        }

        // Verifica se existe algum dado de user a verificar
        if (!isset($userdata) || !is_array($userdata)) {

            // liberta qualquer sessão que possa existir sobre o user
            $this->logout();

            return;
        }

        // Passa os dados do post para uma variável
        if ($userdata['post'] === true) {
            $post = true;
        } else {
            $post = false;
        }

        // Remove a chave post do array userdata
        unset($userdata['post']);

        // Verifica se existe algo a ser verificado
        if (empty($userdata)) {
            $this->logged_in = false;
            $this->login_error = null;

            // liberta qualquer sessão que possa existir sobre o user
            $this->logout();

            return;
        }

        // Extrai variáveis dos dados do user
        extract($userdata);

        // Verifica se existe um user e senha
        if (!isset($user) || !isset($user_password)) {
            $this->logged_in = false;
            $this->login_error = null;

            // liberta qualquer sessão que possa existir sobre o user
            $this->logout();

            return;
        }

        // Verifica se o user existe na base de dados
		//$user vem do formulário login-view.php
        $query = $this->db->query(
                'SELECT * FROM users WHERE user = ? LIMIT 1', array($user)
        );

        // Verifica a consulta
        if (!$query) {
            $this->logged_in = false;
            $this->login_error = 'Internal error.';

            // liberta qualquer sessão que possa existir sobre o user
            $this->logout();

            return;
        }

        // Obtém os dados da base de user
        $fetch = $query->fetch(PDO::FETCH_ASSOC);

        // Obtém o ID do user e o nome
        $user_id = (int) $fetch['user_id'];
        $user_name = $fetch['user'];

        // Verifica se o ID existe
        if (empty($user_id)) {
            $this->logged_in = false;
            $this->login_error = 'User do not exists.';

            // liberta qualquer sessão que possa existir sobre o user
            $this->logout();

            return;
        }

        // Verifica se a pass enviada pelo user coincide com o hash do BD
		// Autoload carregou o classes/class-PasswordHash
        if ($this->phpass->CheckPassword($user_password, $fetch['user_password'])) {

            // Se for uma sessão, verifica se a sessão coincide com a sessão do BD
            if (session_id() != $fetch['user_session_id'] && !$post) {
                $this->logged_in = false;
                $this->login_error = 'Wrong session ID.';

                // liberta qualquer sessão que possa existir sobre o user
                $this->logout();

                return;
            }

            // Se for um post
            if ($post) {
                // Recria o ID da sessão
                session_regenerate_id();
                $session_id = session_id();

                // Envia os dados de user para a sessão
                $_SESSION['userdata'] = $fetch;

                // Atualiza a senha
                $_SESSION['userdata']['user_password'] = $user_password;

                // Atualiza o ID da sessão
                $_SESSION['userdata']['user_session_id'] = $session_id;

                // Atualiza o ID da sessão na base de dados
                $query = $this->db->query(
                        'UPDATE users SET user_session_id = ? WHERE user_id = ?', array($session_id, $user_id)
                );
            }

            // Obtém um array com as permissões de user
            $_SESSION['userdata']['user_permissions'] = unserialize($fetch['user_permissions']);

            // Configura a propriedade dizendo que o user está logado
            $this->logged_in = true;
            $this->user_name = $user_name;

            // Configura os dados do user para $this->userdata
            $this->userdata = $_SESSION['userdata'];

            // Verifica se existe uma URL para redirecionar o user
            if (isset($_SESSION['goto_url'])) {
                // Passa a URL para uma variável
                $goto_url = urldecode($_SESSION['goto_url']);

                // Remove a sessão com a URL
                unset($_SESSION['goto_url']);

                // Redireciona para a página
                echo '<meta http-equiv="Refresh" content="0; url=' . $goto_url . '">';
                echo '<script type="text/javascript">window.location.href = "' . $goto_url . '";</script>';
                //header( 'location: ' . $goto_url );
            }

            return;
        } else {
            // O user não está logado
            $this->logged_in = false;

            // A senha não coincide
            $this->login_error = 'Password does not match.';

            // Remove tudo
            $this->logout();

            return;
        }
    }

    protected function logout($redirect = false) {
        // Remove todas as entradas do array  $_SESSION['userdata']
        $_SESSION['userdata'] = array();
        // Liberta a variavel de sessã
        unset($_SESSION['userdata']);
        // Renova o ID
        session_regenerate_id();
        if ($redirect === true) {
            // Redireciona o user para a pagina de login
            $this->goto_login();
        }
    }

    /**     * Vai para a página de login     */
    protected function goto_login() {
        // Verifica se a URL da HOME está configurada
        if (defined('HOME_URI')) {
            // Configura a URL de login
            $login_uri = HOME_URI . '/login';
            // A página em que o user estava
            $_SESSION['goto_url'] = urlencode($_SERVER['REQUEST_URI']);
            // Redireciona
           /* echo '<meta http-equiv="Refresh" content="0; url=' . $login_uri . '">';
            echo '<script type="text/javascript">window.location.href = "' . $login_uri . '";</script>';
            // header('location: ' . $login_uri);*/
			 $this->refresh($login_uri);
        }
        return;
    }
	
	private function refresh($page = null){
		echo "-----------><br>------>";
		// Redireciona
            echo '<meta http-equiv="Refresh" content="0; url=' . $page . '">';
            echo '<script type="text/javascript">window.location.href = "' . $page . '";</script>';
            //header('location: ' . $page_uri);
	}

    final protected function goto_page($page_uri = null) {
        if (isset($_GET['url']) && !empty($_GET['url']) && !$page_uri) {
            // Configura a URL
            $page_uri = urldecode($_GET['url']);
        }

        if ($page_uri) {
            $this->refresh($page_uri);
			return;
        }
    }

    final protected function check_permissions($required = 'any', $user_permissions = array('any')) {
        if (!is_array($user_permissions)) {
            return;
        }

        // Se o user não tiver permissão
        if (!in_array($required, $user_permissions)) {
            // Retorna falso
            return false;
        } else {
            return true;
        }
    }

}
