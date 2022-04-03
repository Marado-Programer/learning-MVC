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
        $this->userData = $this->controller->userData;
    }

    public function listProjects()
    {
        $id = $where = $queryLimit = null;

        if (is_numeric(checkArray($this->parameters, 0))) {
            $id = array(checkArray($this->parameters, 0));
            $where = "WHERE projectID = ?";
        }

        $page = (!empty($this->parameters[1]) ? $this->parameters[1] : 1) - 1;

        $offset = $this->postsPerPage * $page;

        if (empty($this->noLimits))
            $queryLimit = "LIMIT $offset, {$this->postsPerPage}";

        $query = $this->db->query(
            "SELECT * FROM project $where ORDER BY projectID DESC $queryLimit",
            $id
        );

        return $query->fetchAll();
    }

    public function getProjects()
    {
        if (checkArray($this->parameters, 0) != 'edit')
            return;

        if (!is_numeric(checkArray($this->parameters, 1)))
            return;

        $projectID = checkArray($this->parameters, 1);

        if ('POST' == $_SERVER['REQUEST_METHOD']
            && !empty($_POST['insert-project'])
        ) {
            unset($_POST['insert-project']);

            $date = checkArray($_POST, 'exe-date');
            $newDate = $this->convertDate($date);
            $_POST['exe-date'] = $newDate;

            $image = $this->uploadImage();

            if ($image)
                $_POST['image'] = $image;

            $query = $this->db->update(
                'projects',
                'projectID',
                $projectID,
                $_POST
            );

            if ($query)
                $this->form_msg = <<<'HTML'
                <p>Found projects!</p>
                HTML;

            $query = $this->db->query(
                'SELECT * FROM projects WHERE projectID = ? LIMIT 1',
                array($projectID)
            );

            $fetchedData = $query->fetch();

            if (empty($fetchedData))
                return;

            $this->formData = $fetchedData;
        }
    }

    public function insertProject()
    {
        if ('POST' != $_SERVER['REQUEST_METHOD']
            || empty($_POST['insert-project'])
        )
            return;

        if (checkArray($this->parameters, 0) == 'edit')
            return;

        if (is_numeric(checkArray($this->parameters, 1)))
            return;

        $_POST['image'] = $this->uploadImage();

        unset($_POST['insert-project']);

        $data = checkArray($_POST, 'exe-date');

        $query = $this->db->insert('project', $_POST);

        if ($query) {
            $this->form_msg = <<<'HTML'
            <p>Project successfully created!</p>
            HTML;
            return;
        }

        $this->form_msg = <<<'HTML'
        <p>Error creating project</p>
        HTML;
    }

    public function deleteProject()
    {
        if (checkArray($this->parameters, 0) != 'delete')
            return;

        if (!is_numeric(checkArray($this->parameters, 1)))
            return;

        if (checkArray($this->parameters, 2) != 'confirm') {
            $message = "<p>Do you want to delete this project?</p>";
            $message .= '<p><a href="' . $_SERVER['REQUEST_URI'] . '/confirm/">Yes</a>&nbsp;|&nbsp;<a href="' . HOME_URI . '/Projetos/adm">No</a></p>';
            return $message;
        }

        $projectID = (int) checkArray($this->parameters, 1);

        $query = $this->db->delete('project' , 'projectID', $projectID);
        echo '<meta http-equiv="Refresh" content="0; url' . HOME_URI . '/Projects/admin/">';
        echo '<meta http-equiv="Refresh" content="0; url=' . HOME_URI . '/Projects/admin/" />';
        echo '<script type="text/javascript">window.location.href = "' . HOME_URI . '/Projects/admin/";</script>';
        header('location: '. HOME_URI . '/Projects/admin/');
    }
}

