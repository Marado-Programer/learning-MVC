<?php if (!defined('ROOT_PATH')) exit ?>

        <tr>
            <th scope="row" id="name"><?=$association->name?>
            <td ><?=$association->address?>
            <td ><?=$association->telephone?>
            <td ><?=$association->partners['president']->realName?>
            <td ><?=count($association->partners)?>
            <form method="post"
                action="#">
            <td class="space"><p><input type="hidden" name="association[id]" value="<?=$association->id?>" /></p>
            <td class="actions"><p><a href="<?=HOME_URI?>/@<?=$association->nickname?>">Visit page</a><?php if (UserSession::getUser()->loggedIn && !in_array(clone UserSession::getUser(), $association->partners)): ?><br />
            <button name="association[action]" value="enter">Enter Association</button><?php endif ?></p>
            </form>
