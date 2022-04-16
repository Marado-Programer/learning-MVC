<?php defined('ROOT_PATH') OR exit(); ?>

<main>

<?php
    $iterator = $this->news->getIterator();
    while ($iterator->valid()) {
        $news = $iterator->current();
        require VIEWS_PATH . '/news/document.php';
        $iterator->next();
    }
?>

</main>
