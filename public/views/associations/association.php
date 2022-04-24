<?php if (!defined('ROOT_PATH')) exit ?>
        <tr>
            <th scope="row" id="name"><?=$association->name?>
            <td ><?=$association->address?>
            <td ><?=$association->telephone?>
            <td ><?=$association->president->getRealName()?>
            <td ><?=count($association->getPartners())?>
            <form method="post"
                action="#">
            <td class="space"><p><input type="hidden" name="association[id]" value="<?=$association->getID()?>" /></p>
            <td class="actions"><p><a href="<?=HOME_URI?>/@<?=$association->nickname?>">Visit page</a><?php if (UserSession::getUser()->isLoggedIn() && !in_array(clone UserSession::getUser(), $association->getPartners())): ?><br />
            <button name="association[action]" value="enter">Enter Association</button><?php endif ?><?php if (UserSession::getUser()->isLoggedIn() && UserSession::getUser() instanceof Partner && $association->checkIfAdmni(clone UserSession::getUser())): ?><br />
            <a href="<?=HOME_URI?>/@<?=$association->nickname?>/admni">Admnistrator Panel</a><?php endif ?></p>
            </form>

