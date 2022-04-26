<?php if (!defined('ROOT_PATH')) exit ?>
        <tr>
            <th scope="row" id="name"><?=$association->name?>
            <td ><?=$association->address?>
            <td ><?=$association->telephone?>
            <td ><?=$association->president->getRealName()?>
            <td ><?=count($association->getPartners())?>
            <form method="post"
                action="#">
            <td class="space"><p><input type="hidden" name="enterAssociation[id]" value="<?=$association->getID()?>" /></p>
            <td class="actions"><p><a href="<?=HOME_URI?>/@<?=$association->nickname?>">Visit page</a>
<?php
if (UserSession::getUser()->isLoggedIn()) {
    $isPartner = false;
    foreach ($association->getPartners() as $partner)
        if ($partner->getID() == UserSession::getUser()->getID()) {
            $isPartner = true;
            break;
        }
    if (!$isPartner)
        echo '<button name="enterAssociation[enter]" value="enter">Enter Association</button>';
}
?>
<br />
            </form>
            <?php
            if (UserSession::getUser()->isLoggedIn() && UserSession::getUser() instanceof Partner) {
                $hasQuotaToPay = false;
                foreach (UserSession::getUser()->getQuotas() as $i => $quota) {
                    if ($quota->association->getID() == $association->getID() && $quota->endDate < new DateTime()): ?>
                            <form method="post"
                                action="<?=HOME_URI?>/pay-quota">
                                <p><label><input type="hidden" name="payQuota[user]" value="<?=UserSession::getUser()->getID()?>"/></label></p>
                                <p><label><input type="hidden" name="payQuota[association]" value="<?=$quota->association->getID()?>"/></label></p>
                                <p>Quota: paid:<?=$quota->payed?>$/<?=$quota->price?><br /><label>Pay: <input type="number" name="payQuota[quantity]" step="0.001" min="0" max="<?=UserSession::getUser()->getWallet()?>" size="9" /></label><button name="payQuota[pay]">Pay</button></p>
                            </form>
                        <?php $hasQuotaToPay = true;
                        break;
                    endif;
                }
                if ($association->checkIfAdmni(UserSession::getUser()) && !$hasQuotaToPay): ?><br />
                <a href="<?=HOME_URI?>/@<?=$association->nickname?>/admni">Admnistrator Panel</a><?php endif; } ?></p>
