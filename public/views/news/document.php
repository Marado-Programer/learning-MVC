<article>
    <h1><?=$news->title?></h1>
    <footer>
        <p><a href="<?=HOME_URI . '/@' . $news->association->name?>">Association: <?=$news->association->name?></a></p>
        <p>Author: <?=$news->author->username?></p>
    </footer>
    <img src="<?=HOME_URI . '/public/uploads/' . $news->image?>" />
    <?=$news->article?>
</article>