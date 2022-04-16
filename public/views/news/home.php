<?php defined('ROOT_PATH') OR exit(); ?>

<main>

<p><?=$this->date->format(DateTimeInterface::RFC7231)?> to <?=$this->date->add(new DateInterval('P1' . $this->use))->format(DateTimeInterface::RFC7231)?></p>

<ol>
<?php
    $iterator = $this->news->getIterator(NewsList::$NEWER_ORDER);
    while ($iterator->valid()) {
        $news = $iterator->current();
        echo '<li><a href="', HOME_URI . '/article/' . $news->id, '">', $news->title, '</a></li>';
        $iterator->next();
    }
?>
</ol>

</main>
