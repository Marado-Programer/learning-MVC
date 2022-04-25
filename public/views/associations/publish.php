<?php defined('ROOT_PATH') OR exit() ?>

<section id="publish">

<header>
<h3>Publish News</h3>
</header>

<ul>
<form method="post"
    action="#">
    <?php
        $this->model->addUnpublishedNews($this->association->getID());
        $iterator = $this->unpublishedNews->getIterator(NewsList::$NEWER_ORDER);
        while ($iterator->valid()) {
            $news = $iterator->current();
            echo '<li>' . $news . '<button type="submit" name="edit[news]" value="' . $news->id . '">Edit</button><button type="submit" name="publish[news]" value="' . $news->id . '">Publish</button></li><hr />';
            $iterator->next();
        }
    ?>
</form>
</ul>

</section>
