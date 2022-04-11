<?php if (!defined('ROOT_PATH')) exit ?>

<main>
    <h1>Projects</h1>
    <section>
    <link href="<?=HOME_URI?>/public/style/css/projects.css" rel="stylesheet" />
    <?php
    foreach ($this->model->listProjects() as $project)
        require ROOT_PATH . '/public/views/projects/project.php';
    ?>
    </section>
</main>

