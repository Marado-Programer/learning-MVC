<?php if (!defined('ROOT_PATH')) exit ?>

<article>
    <div class="title-flag">
    <h2>Projeto Fixolas</h2>
    <p class="author">Torres</p>
    </div>
    <div>
    <a href="<?=ROOT_PATH?>/Projects/index/<?=$project['id']?>"><?=$project['description']?></a>
    <?php if (is_numeric(checkArray($this->model->parameters, 0))): ?>
    <?php if ($this->previousPage = true): ?>
    <a href="<?=ROOT_PATH?>/Projects/index/">Back</a>
    <?php endif ?>
    <p><?=$model->inverte_data($project['executionDate'])?></p>
    <img src="<?=UPLOAD_PATH?><?=$project['image']?>" />
    <?=$project['link']?>
    <?php endif ?>
    </div>
</article>

