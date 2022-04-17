<?php defined('ROOT_PATH') OR exit(); ?>

<main>

<ol>
<?php
    $iterator = $this->events->getIterator(EventsList::$END_FIRST_ORDER);
    while ($iterator->valid()) {
        $event = $iterator->current();
        print_r($event);
        $iterator->next();
    }
?>
</ol>

</main>
