<?php defined('ROOT_PATH') OR exit(); ?>

<main>

<form method="post"
    action="#"
    enctype="multipart/form-data">
</form>

<ol>
<?php
    $i = 0;
    $iterator = $this->events->getIterator(EventsList::$END_FIRST_ORDER);
    while ($iterator->valid()) {
        $event = $iterator->current();
        require VIEWS_PATH . '/events/event.php';
        $iterator->next();
    }
?>
</ol>

</main>