<?php

class UserRegisterModel {

    public $form_data;
    public $form_msg;
    public $db;

    public function __construct($db = false) {
        $this->db = $db;
    }

    public function validate_register_form() {

        // Configura os dados do formulário
        $this->form_data = array();

        // Verifica se algo foi passado
        if ('POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST)) {

            // Faz o loop dos dados do post
            foreach ($_POST as $key => $value) {

                // Configura os dados do post para a propriedade $form_data
                $this->form_data[$key] = $value;

                // Sem campos em branco
                if (empty($value)) {

                    // Configura a mensagem
                    $this->form_msg = '<p class="form_error">There are empty fields. Data has not been sent.</p>';
                    return;
                }
            }
        } else {

            // Termina se nada foi enviado
            return;
        }

        // Verifica se a propriedade $form_data foi preenchida
        if (empty($this->form_data)) {
            return;
        }

        // Verifica se o user existe
        $db_check_user = $this->db->query(
                'SELECT * FROM `users` WHERE `user` = ?', array(
            chk_array($this->form_data, 'user')
                )
        );

        // Verifica se a consulta foi realizada com sucesso
        if (!$db_check_user) {
            $this->form_msg = '<p class="form_error">Internal error.</p>';
            return;
        }

        // Obtém os dados da base de dados
        $fetch_user = $db_check_user->fetch();

        // Configura o ID do user
        $user_id = $fetch_user['user_id'];

        // Precisaremos de uma instância da classe Phpass
        // veja http://www.openwall.com/phpass/
        $password_hash = new PasswordHash(8, FALSE);

        // Cria o hash da senha
        $password = $password_hash->HashPassword($this->form_data['user_password']);

        // Verifica se as permissões tem algum valor inválido: 
        // 0 a 9, A a Z e , . - _
        if (preg_match('/[^0-9A-Za-z\,\.\-\_\s ]/is', $this->form_data['user_permissions'])) {
            $this->form_msg = '<p class="form_error">Use just letters, numbers and a comma for permissions.</p>';
            return;
        }

        // Faz um trim nas permissões
        $permissions = array_map('trim', explode(',', $this->form_data['user_permissions']));

        // Remove permissões duplicadas
        $permissions = array_unique($permissions);

        // Remove valores em branco
        $permissions = array_filter($permissions);

        // Serializa as permissões
        $permissions = serialize($permissions);


        // Se o ID do user não estiver vazio, atualiza os dados
        if (!empty($user_id)) {

            $query = $this->db->update('users', 'user_id', $user_id, array(
                'user_password' => $password,
                'user_name' => chk_array($this->form_data, 'user_name'),
                'user_session_id' => md5(time()),
                'user_permissions' => $permissions,
            ));

            // Verifica se a consulta está OK e configura a mensagem
            if (!$query) {
                $this->form_msg = '<p class="form_error">Internal error. Data has not been sent.</p>';
                return;
            } else {
                $this->form_msg = '<p class="form_success">User successfully updated.</p>';
                return;
            }
            // Se o ID do user estiver vazio, insere os dados
        } else {

            // Executa a consulta 
            $query = $this->db->insert('users', array(
                'user' => chk_array($this->form_data, 'user'),
                'user_password' => $password,
                'user_name' => chk_array($this->form_data, 'user_name'),
                'user_session_id' => md5(time()),
                'user_permissions' => $permissions,
            ));

            // Verifica se a consulta está OK e configura a mensagem
            if (!$query) {
                $this->form_msg = '<p class="form_error">Internal error. Data has not been sent.</p>';

                // Termina
                return;
            } else {
                $this->form_msg = '<p class="form_success">User successfully registered.</p>';

                // Termina
                return;
            }
        }
    }

// validate_register_form


    public function get_register_form($user_id = false) {

        // O ID de user que vamos pesquisar
        $s_user_id = false;

        // Verifica se passou ID ao método
        if (!empty($user_id)) {
            $s_user_id = (int) $user_id;
        }

        // Verifica se existe um ID de user
        if (empty($s_user_id)) {
            return;
        }

        // Verifica na base de dados
        $query = $this->db->query('SELECT * FROM `users` WHERE `user_id` = ?', array($s_user_id));

        // Verifica a consulta
        if (!$query) {
            $this->form_msg = '<p class="form_error">User não existe.</p>';
            return;
        }

        // Obtém os dados da consulta
        $fetch_userdata = $query->fetch();

        // Verifica se os dados da consulta estão vazios
        if (empty($fetch_userdata)) {
            $this->form_msg = '<p class="form_error">User do not exists.</p>';
            return;
        }

        // Configura os dados do formulário
        foreach ($fetch_userdata as $key => $value) {
            $this->form_data[$key] = $value;
        }

        // Por questões de segurança, a senha só poderá ser atualizada
        $this->form_data['user_password'] = null;

        // Remove a serialização das permissões
        $this->form_data['user_permissions'] = unserialize($this->form_data['user_permissions']);

        // Separa as permissões por vírgula
        $this->form_data['user_permissions'] = implode(',', $this->form_data['user_permissions']);
    }

// get_register_form


    public function del_user($parametros = array()) {

        // O ID do user
        $user_id = null;

        // Verifica se existe o parâmetro "del" na URL
        if (chk_array($parametros, 0) == 'del') {

            // Mostra uma mensagem de confirmação
            echo '<p class="alert">Tem certeza que deseja apagar este registo?</p>';
            echo '<p><a href="' . $_SERVER['REQUEST_URI'] . '/confirma">Sim</a> | 
			<a href="' . HOME_URI . '/user-register">Não</a> </p>';

            // Verifica se o valor do parâmetro é um número
            if (
                    is_numeric(chk_array($parametros, 1)) && chk_array($parametros, 2) == 'confirma'
            ) {
                // Configura o ID do user a ser apagado
                $user_id = chk_array($parametros, 1);
            }
        }

        // Verifica se o ID não está vazio
        if (!empty($user_id)) {

            // O ID precisa ser inteiro
            $user_id = (int) $user_id;

            // Elimina o user
            $query = $this->db->delete('users', 'user_id', $user_id);

            // Redireciona para a página de listagem
            echo '<meta http-equiv="Refresh" content="0; url=' . HOME_URI . '/user-register/">';
            echo '<script type="text/javascript">window.location.href = "' . HOME_URI . '/user-register/";</script>';
            return;
        }
    }

// del_user


    public function get_user_list() {

        // Seleciona os dados da base de dados 
        $query = $this->db->query('SELECT * FROM `users` ORDER BY user_id DESC');

        // Verifica se a consulta está OK
        if (!$query) {
            return array();
        }
        // Preenche a tabela com os dados do user
        return $query->fetchAll();
    }

// get_user_list
}
