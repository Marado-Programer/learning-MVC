<?php if (!defined('ROOT_PATH')) exit ?>

<main>
    <h1>Projects</h1>
    <section>
    <?php
    foreach ($this->model->listProjects() as $project)
        require ROOT_PATH . '/public/views/projects/project.php';
    ?>
    </section>
</main>

