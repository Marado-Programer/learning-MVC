<?php defined('ROOT_PATH') OR exit() ?>

<body>

<p><input type="hidden" value="<?=UserSession::getUser()->getID()?>" id="idOfUser" /></p>

<nav>

<ul>
    <li><a href="<?=HOME_URI?>">Home</a>
    <li><a href="<?=HOME_URI?>/user-register">Sign In</a>
    <li><?php if (UserSession::getUser()->isLoggedIn()): ?>
        <a href="<?=HOME_URI?>/login/delete">Log out</a>
        <form method="post"
            action="<?=HOME_URI?>/add-to-wallet">
            <p><label><input type="hidden" name="deposit[user]" value="<?=UserSession::getUser()->getID()?>"/></label></p>
            <p>Wallet: <?=UserSession::getUser()->getWallet()?>$&nbsp;|&nbsp;<label>Quantity: <input type="number" name="deposit[quantity]" step="0.001" min="0" /></label><button>Deposit</button></p>
        </form>
        <?php else: ?><a href="<?=HOME_URI?>/login">Log in</a><?php endif ?></li>
    <li><a href="<?=HOME_URI?>/associations">Associations</a>
    <li><a href="<?=HOME_URI?>/news">News</a>
    <li><a href="<?=HOME_URI?>/events">Events</a>
</ul>

</nav>
