<?php

class MainController extends UserLogin {

    public $db;
    public $phpass;
    public $title;
    public $login_required = false;
    public $permission_required = 'any';
    public $parametros = array();

    public function __construct($parametros = array()) {
        // Instancia do DB
        $this->db = new SystemDB();
        // Phpass
        $this->phpass = new PasswordHash(8, false);
        // Parâmetros
        $this->parametros = $parametros;
        // Verifica o login classe - UserLogin
        $this->check_userlogin();
    }
// __construct
    public function load_model($model_name = false) {
        // Um ficheiro deverá ser enviado
        if (!$model_name)
            return;
        // Garante que o nome do modelo tenha letras minúsculas
        $model_name = strtolower($model_name);
        // Inclui o ficheiro
        $model_path = ABSPATH . '/models/' . $model_name . '.php';
        // Verifica se o ficheiro existe
        if (file_exists($model_path)) {
            // Inclui o ficheiro
            require_once $model_path;
            // Remove os caminhos do ficheiro (se tiver algum)
            $model_name = explode('/', $model_name);
            // Captura o final do caminho(nome do modelo)
            $model_name = end($model_name);
            // Remove caracteres inválidos do nome do ficheiro
            $model_name = preg_replace('/[^a-zA-Z0-9]/is', '', $model_name);
            // Verifica se a classe existe
            if (class_exists($model_name)) {
                // Retorna um objeto da classe
                return new $model_name($this->db, $this);
            }
            return;
        } // load_model
    }
// load_model
}
// class MainController