<?php

if (!defined('ROOT_PATH'))
    exit;

if ($this->loginRequired && !UserSession::getUser()->loggedIn)
    return;
?>

<nav>

<?php if (UserSession::getUser()->loggedIn): ?>
    <a href="<?=HOME_URI?>/login/delete/">Logout</a>
<?php else: ?>
    <a href="<?=HOME_URI?>/login/">Login</a>
<?php endif ?>
    <p>Welcome <?=UserSession::getUser()->username?></p>
    <ul>
        <li><a href="<?=HOME_URI?>">Home</a></li>
        <li><a href="<?=HOME_URI?>/userRegister">Sign In</a></li>
        <li><a href="<?=HOME_URI?>/associations">Associations</a></li>
        <li><a href="<?=HOME_URI?>/news">News</a></li>
        <li><a href="<?=HOME_URI?>/events">Events</a></li>
        <li><a href="<?=HOME_URI?>/projects">Projetos</a></li>
        <li><a href="<?=HOME_URI?>/projects/adm">Gerir Projetos</a></li>
    </ul>
</nav>
