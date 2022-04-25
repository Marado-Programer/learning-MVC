<?php defined('ROOT_PATH') OR exit() ?>

        <tr>
            <th scope="row"><?=$association->name?>
            <td ><?=$association->address?>
            <td ><?=$association->telephone?>
            <td ><?=$association->president->getRealName()?>
            <td ><?=count($association->getPartners())?>
            <form method="post"
                action="#">
            <td class="space"><p><input type="hidden" name="payQuota[association]" value="<?=$association->getID()?>" /></p>
            <td class="actions"><p><a href="<?=HOME_URI?>/@<?=$association->nickname?>">Visit page</a><br />
            <?php
            foreach (UserSession::getUser()->getQuotas() as $i => $quota)
                if ($quota->association->getID() == $association->getID() && $quota->endDate < new DateTime()) {
                    echo '<button name="payQuota[pay]" value="' . $i . '">Pay Quota</button>';
                    break;
                }
            if ($association->checkIfAdmni(UserSession::getUser()))
                echo '<a href="' . HOME_URI . '/@' . $association->nickname . '/admni">Admnistrator Panel</a>';
            ?>
            </p>
            </form>

