<?php if (!defined('ROOT_PATH')) exit ?>

        <tr>
            <th scope="row"><?=$association->name?>
            <td ><?=$association->address?>
            <td ><?=$association->telephone?>
            <td ><?=$association->partners['president']->realName?>
            <td ><?=count($association->partners)?>
            <form method="post"
                action="#">
            <td class="space"><p><input type="hidden" name="association[name]" value="<?=$association->nickname?>" /></p>
            <td class="actions"><p><a href="<?=HOME_URI?>/@<?=$association->nickname?>">Visit page</a><?php if ($association->checkIfAdmin(clone UserSession::getUser())): ?><br />
            <a href="<?=HOME_URI?>/@<?=$association->nickname?>/admni">Admnistrator Panel</a><?php endif ?></p>
            </form>
