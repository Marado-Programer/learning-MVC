<?php

defined('ROOT_PATH') OR exit();

if ($this->loginRequired && !UserSession::getUser()->isLoggedIn())
    UsersManager::getTools()->getRedirector->redirect(HOME_URI . '/login');

?>

<nav>

<?php if (UserSession::getUser()->isLoggedIn()): ?>
    <p><a href="<?=HOME_URI?>/login/delete/">Logout</a></p>
    <form method="post"
        action="<?=HOME_URI?>/add-to-wallet">
        <p><label><input type="hidden" name="deposit[user]" value="<?=UserSession::getUser()->getID()?>"/></label></p>
        <p>Wallet: <?=UserSession::getUser()->getWallet()?>$&nbsp;|&nbsp;<label>Quantity: <input type="number" name="deposit[quantity]" step="0.001" min="0" size="9" /></label><button>Deposit</button></p>
    </form>
<?php else: ?>
    <a href="<?=HOME_URI?>/login/">Login</a>
<?php endif ?>
    <p>Welcome <?=UserSession::getUser()->username?></p>
    <ul>
        <li><a href="<?=HOME_URI?>">Home</a></li>
        <li><a href="<?=HOME_URI?>/user-register">Sign In</a></li>
        <li><a href="<?=HOME_URI?>/associations">Associations</a></li>
        <li><a href="<?=HOME_URI?>/news">News</a></li>
        <li><a href="<?=HOME_URI?>/events">Events</a></li>
    </ul>
</nav>

