<?php if (!defined('ROOT_PATH')) exit ?>

        <tr>
            <th scope="row"><?=$association->name?>
            <td ><?=$association->address?>
            <td ><?=$association->telephone?>
            <td ><?=$association->partners['president']->realName?>
            <td ><?=count($association->partners)?>
            <form method="post"
                action="#">
            <td class="space"><p><input type="hidden" name="payQuota[association]" value="<?=$association->id?>" /></p>
            <td class="actions"><p><a href="<?=HOME_URI?>/@<?=$association->nickname?>">Visit page</a><br />
            <?php
            foreach (UserSession::getUser()->userDues as $i => $quota)
                if ($quota->association->id == $association->id && $quota->endDate < new DateTime()) {
                    echo '<button name="payQuota[pay]" value="' . $i . '">Pay Quota</button>';
                    break;
                }
            if ($association->checkIfAdmin(UserSession::getUser()))
                echo '<a href="' . HOME_URI . '/@' . $association->nickname . '/admni">Admnistrator Panel</a>';
            ?>
            </p>
            </form>
