<?php

/**
 * 
 */

class ProjetosAdmModel extends MainModel
{
    public $postsPerPage = 5;

    public function __construct($db = false, $controller = null)
    {
        $this->db = $db;
        $this->controller = $controller;
        $this->parameters = $this->controller->parameters;
        $this->userdata = $this->controller->userdata;
    }

    public function listProjects()
    {
        $id = $where = $query_limit = null;

        if (is_numeric(checkArray($this->parameters, 0))) {
            $id = array(checkArray($this->parameters, 0));
            $where = " WHERE idProjeto = ? ";
        }

        $page = (!empty($this->parameters[1]) ? $this->parameters[1] : 1)--;

        $offset = $this->postsPerPage * $page;

        if (empty($this->noLimits))
            $queryLimit = " LIMIT $offset, {$this->postsPerPage}";

        $query = $this->db->query(
            "SELECT * FROM projeto $where ORDER BY idProjecto DESC $queryLimit",
            $id
        );

        return $query->fetchAll();
    }

    public function obtem_projetos()
    {
        if (checkArray($this->parameters, 0) != 'edit')
            return;

        if (!is_numeric(checkArray($this->parameters, 1)))
            return;

        $projectID = checkArray($this->parameters, 1);

        if ('POST' == $_SERVER['REQUEST_METHOD']
            && !empty($_POST['insert-project'])) {
            unset($_POST['insert-project']);
            $date = checkArray($_POST, 'dataExec');
            $newDate = $this->inverte_data($date);
            $_POST['dataExec'] = $newDate;
            $image = $this->uploadImage();
            if ($image)
                $_POST['image'] = $image;

            $query = $this->db->update(
                'projeto',
                'idProjeto',
                $projectID,
                $_POST
            );
            if ($query)
                $this->form_msg = '<p class="success">projeto atualizado com sucesso!</p>';

            $query = $this->db->query(
                'SELECT * FROM projeto WHERE idProjeto = ? LIMIT 1',
                array($projectID)
            );

            $fetchedData = $query->fetch();

            if (empty($fetchedData))
                return;

            $this->form_data = $fetchedData;
        }
    }

    public function insertProject()
    {
        if ('POST' != $_SERVER['REQUEST_METHOD']
            || empty($_POST['insert-project']))
            return;

        if (checkArray($this->parameters, 0) == 'edit')
            return;

        if (is_numeric(checkArray($this->parameters, 1)))
            return;

        $image = $this->uploadImage();
        unset($_POST['insert-project']);
        $_POST['image'] = $image;
        $data = checkArray($_POST, 'dataExec');
        $query = $this->db->insert('projeto', $_POST);

        if ($query) {
            $this->form_msg = '<p class="success">projeto atualizado com sucesso!</p>';
            return;
        }

        $this->form_msg = '<p class="success">erro ao enviar dados</p>';
    }

    public function deleteProject()
    {
        if (checkArray($this->parameters, 0) != 'del')
            return;

        if (!is_numeric(checkArray($this->parameters, 1)))
            return;

        if (checkArray($this->parameters, 2) != 'confirma') {
            $message = '<p class="alert">Queres?</p>';
            $message .= "<p><a href=\"{$_SERVER['REQUEST_URI']}/confirma/\">Y</a> | <a href=\"" . HOME_URI . "/Projetos/adm\">N</a></p>";
            return $message;
        }

        $projectID = (int) checkArray($this->parameters, 1);

        $query = $this->db->delete('projeto' , 'idProjeto', $projectID);
        echo '<meta http-equiv="Refresh" content="0; url' . HOME_URI . '/Projetos/adm/">';
    }
}

