<?php if (!defined('ROOT_PATH')) exit ?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Ubuntu&display=swap');

* {
    margin: 0;
    padding: 0;
    font-family: 'Ubuntu', sans-serif;
}

h1 {
    font-size: 67px;
}

h2 {
    font-size: 50px;
}

h3 {
    font-size: 37px;
}

h4 {
    font-size: 28px;
}

h5 {
    font-size: 21px;
}

h6, p {
    font-size: 16px;
}

section {
    display: flex;
    justify-content: space-between;
}

section > article {
    background: #fddbc7 no-repeat center center url("");
    background-size: cover;
    position: relative;
    width: 640px;
    height: 379px;
    border: 5px solid black;
    border-radius: 12px;
    margin: 12px;
}

.title-flag {
    position: absolute;
    width: calc(100% - 12px - 5px);
    bottom: 12px;
    left: -12px;
    background-color: #2166ac;
    padding: 16px;
    border-radius: 0 0 0 12px;
}

.author::before {
    content: "[ Author: ";
}

.author::after {
    content: " ]";
}
</style>

<main>
    <h1>Projects</h1>
    <section>
    <?php
    foreach ($this->model->listProjects() as $project)
        require ROOT_PATH . '/public/views/projects/project.php';
    ?>
    </section>
</main>

