<?php if (!defined('ROOT_PATH')) exit ?>

<section id="search">

<header>
<h2>Search</h2>
</header>

<link href="<?=HOME_URI?>/public/style/css/associations.css" rel="stylesheet" />
<?php
foreach ($this->model->listAssociations() as $association)
    require VIEWS_PATH . '/associations/association.php';
?>

</section>
