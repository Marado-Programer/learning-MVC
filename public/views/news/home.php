<?php defined('ROOT_PATH') OR exit(); ?>

<main>

<p><?=$this->date->format(DateTimeInterface::RFC7231)?> to <?=$this->date->add(new DateInterval('P1' . $this->use))->format(DateTimeInterface::RFC7231)?></p>

<ol>
<?php
    $iterator = $this->news->getIterator(NewsList::$NEWER_ORDER);
    while ($iterator->valid()) {
        $news = $iterator->current();
        echo '<li>' . $news . '</li><hr />';
        $iterator->next();
    }
?>
</ol>

<footer>
<p><a href="<?=HOME_URI?>/news/create">Create a News for an Association!</a></p>
</footer>

</main>
