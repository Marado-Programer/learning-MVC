<?php

if (!defined('ROOT_PATH'))
    exit;

if ($this->loginRequired && !$this->userSession->user->loggedIn)
    return;
?>

<nav>

<?php if ($this->userSession->user->loggedIn): ?>
    <a href="<?=HOME_URI?>/login/delete/">Logout</a>
<?php else: ?>
    <a href="<?=HOME_URI?>/login/">Login</a>
<?php endif ?>
    <p>Welcome <?=$this->userSession->user->username?></p>
    <ul>
        <li><a href="<?=HOME_URI?>">Home</a></li>
        <li><a href="<?=HOME_URI?>/userRegister">User register</a></li>
        <li><a href="<?=HOME_URI?>/associations">Associations</a></li>
        <li><a href="<?=HOME_URI?>/news">Noticias</a></li>
        <li><a href="<?=HOME_URI?>/news/adm">Gerir Noticias</a></li>
        <li><a href="<?=HOME_URI?>/projects">Projetos</a></li>
        <li><a href="<?=HOME_URI?>/projects/adm">Gerir Projetos</a></li>
    </ul>
</nav>
