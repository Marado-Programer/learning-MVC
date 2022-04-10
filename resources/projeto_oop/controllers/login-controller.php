<?php

class LoginController extends MainController {

    /**
     * Carrega a página "/views/login/index.php"
     */
    public function index() {
        // Título da página
        $this->title = 'Login';
        // Parametros da função
        $parametros = ( func_num_args() >= 1 ) ? func_get_arg(0) : array();
		

        // Login não tem Model
        /** Carrega os arquivos do view * */
        // /views/_includes/header.php
        require ABSPATH . '/views/_includes/header.php';
        // /views/_includes/menu.php
        require ABSPATH . '/views/_includes/menu.php';
        // /views/login/login-view.php
        require ABSPATH . '/views/login/login-view.php';
        // /views/_includes/footer.php
        require ABSPATH . '/views/_includes/footer.php';
    }

    public function delete() {
        $this->logout();
        // Redireciona para a página de login
        $this->goto_login();
        // Garante que o script não vai passar daqui
        return;
    }

// index
}

// class LoginController