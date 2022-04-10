<?php

class System {

    private $controlador;
    private $acao;
    private $parametros;
    private $not_found = '/includes/404.php';

    public function __construct() {

        // Obtém os valores do controlador, ação e parâmetros da URL.
        // E configura as propriedades da classe.
        $this->get_url_data();

        /**
         * Verifica se o controlador existe. Caso contrário, adiciona o
         * controlador padrão (controllers/home-controller.php) e chama o método index().
         */
        if (!$this->controlador) {

            // Adiciona o controlador padrão
            require_once ABSPATH . '/controllers/home-controller.php';

            // Cria o objeto do controlador "home-controller.php"
            // Este controlador deverá ter uma classe chamada HomeController
            $this->controlador = new HomeController();

            // Executa o método index()
            $this->controlador->index();

            return;
        }

        // Se o ficheiro do controlador não existir, retorn com page 404
        if (!file_exists(ABSPATH . '/controllers/' . $this->controlador . '.php')) {
            // Página não encontrada
            require_once ABSPATH . $this->not_found;

            return;
        }

        // Inclui o ficheiro do controlador
        require_once ABSPATH . '/controllers/' . $this->controlador . '.php';

        // Remove caracteres inválidos do nome do controlador para gerar o nome
        // da classe. Se o ficheiro chamar-se "news-controller.php", a classe deverá
        // se chamar NewsController.
        $this->controlador = preg_replace('/[^a-zA-Z]/i', '', $this->controlador);

        // Se a classe do controlador indicado não existir, retorn com page 404
        if (!class_exists($this->controlador)) {
            // Página não encontrada
            require_once ABSPATH . $this->not_found;

            return;
        } // class_exists
        // Cria o objeto da classe do controlador e envia os parâmentros
        $this->controlador = new $this->controlador($this->parametros);

        // Remove caracteres inválidos do nome da ação (método)
        $this->acao = preg_replace('/[^a-zA-Z]/i', '', $this->acao);

        // Se o método indicado existir, executa o método e envia os parâmetros
        if (method_exists($this->controlador, $this->acao)) {
            $this->controlador->{$this->acao}($this->parametros);

            return;
        } // method_exists
        // Sem ação, chamamos o método index
        if (!$this->acao && method_exists($this->controlador, 'index')) {
            $this->controlador->index($this->parametros);

            return;
        } // ! $this->acao 
        // Página não encontrada
        require_once ABSPATH . $this->not_found;
        return;
    }

// __construct

    /**
     * Obtém parâmetros de $_GET['path']
     *
     * Obtém os parâmetros de $_GET['path'] e configura as propriedades 
     * $this->controlador, $this->acao e $this->parametros
     *
     * A URL deverá ter o seguinte formato:
     * http://www.example.com/controlador/acao/parametro1/parametro2/etc...
     */
    public function get_url_data() {

        // Verifica se o parâmetro path foi enviado
        if (isset($_GET['path'])) {

            // Captura o valor de $_GET['path']
            $path = $_GET['path'];
			echo '->'.$path;

            // Limpa os dados
            $path = rtrim($path, '/'); //remove a '/' caso exista
			echo '->'.$path;
            $path = filter_var($path, FILTER_SANITIZE_URL);
			echo '->'.$path;

            // Cria um array de parâmetros
            $path = explode('/', $path);
			print_r($path);

            // Configura as propriedades
            $this->controlador = chk_array($path, 0);// indice 0 do array, represetenta o controlador, por exemplo: projetos
			echo "<br />".$this->controlador;
            $this->controlador .= '-controller';
            $this->acao = chk_array($path, 1);// indice 1 do array, representa a acção sobre o controlador, exemplo: add, rem

            // Configura os parâmetros
            if (chk_array($path, 2)) {
                unset($path[0]);
                unset($path[1]);

                // Os parâmetros sempre virão após a ação
                $this->parametros = array_values($path);
            }

        }
    }// get_url_data
}
// class System