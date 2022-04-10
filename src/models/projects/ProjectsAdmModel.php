<?php

/**
 * 
 */

class ProjectsAdmModel extends MainModel
{
    public $postsPerPage = 5;

    public function listProjects()
    {
        $id = $where = $queryLimit = null;

        if (is_numeric(checkArray($this->parameters, 0))) {
            $id = array(checkArray($this->parameters, 0));
            $where = "WHERE `projects`.`id` = ?";
        }

        $page = (!empty($this->parameters[1]) ? $this->parameters[1] : 1) - 1;

        $offset = $this->postsPerPage * $page;

        if (empty($this->noLimit))
            $queryLimit = "LIMIT $offset, {$this->postsPerPage}";

        $query = $this->db->query(
            "SELECT * FROM `projects` $where ORDER BY `projects`.`id` DESC $queryLimit",
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
                '`projects`',
                '`projects`.`id`',
                $projectID,
                $_POST
            );

            if ($query)
                $this->form_msg = <<<'HTML'
                <p>Found projects!</p>
                HTML;

            $query = $this->db->query(
                'SELECT * FROM `projects` WHERE `projects`.`id` = ? LIMIT 1',
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

        $query = $this->db->insert('`projects`', $_POST);

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

        $query = $this->db->delete('`projects`' , '`projects`.`id`', $projectID);

        redirect(HOME_URI . '/Projects/adm/');
    }
}

