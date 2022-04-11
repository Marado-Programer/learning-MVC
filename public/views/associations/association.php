<?php if (!defined('ROOT_PATH')) exit ?>

<article>
    <div class="name-flag">
    <h3><?=$association['name']?></h3>
    <p class="author">Torres</p>
    </div>
    <div>
    <a href="<?=ROOT_PATH?>/associations/more/<?=$association['id']?>">description</a>
    <?php if (is_numeric(checkArray($this->model->parameters, 0))): ?>
    <?php if ($this->previousPage = true): ?>
    <a href="<?=ROOT_PATH?>/Projects/index/">Back</a>
    <?php endif ?>
    <p><?=$model->inverte_data($association['executionDate'])?></p>
    <img src="<?=UPLOAD_PATH?><?=$association['image']?>" />
    <?=$project['id']?>
    <?php endif ?>
    </div>
</article>

