<?php defined('ROOT_PATH') OR exit() ?>

<section id="<?php echo !isset($SESSION_['editNews']) ? 'create' : 'edit' ?>">

<header>
<h3><?php echo !isset($SESSION_['editNews']) ? 'Create' : 'Edit' ?> News</h3>
</header>

<p><a href="<?=HOME_URI?>/news/create/<?=$this->association->nickname?>">Create a News for an Association!</a></p>

</section>
