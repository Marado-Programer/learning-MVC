<?php

if (!defined('ROOT_PATH'))
    exit;

if ($this->loginRequired && !$this->loggedIn)
    return;
?>

<nav>

<?php if ($this->loggedIn): ?>
    <p>Welcome <?=$this->username?></p>
    <a href="<?=HOME_URI?>/Login/delete/">Logout</a>
<?php else: ?>
    <a href="<?=HOME_URI?>/Login/">Login</a>
    <ul>
        <li><a href="<?=HOME_URI?>">Home</a></li>
        <li><a href="<?=HOME_URI?>/UserRegister">User register</a></li>
        <li><a href="<?=HOME_URI?>/News">Noticias</a></li>
        <li><a href="<?=HOME_URI?>/News/adm">Gerir Noticias</a></li>
        <li><a href="<?=HOME_URI?>/Projects">Projetos</a></li>
        <li><a href="<?=HOME_URI?>/Projects/adm">Gerir Projetos</a></li>
    </ul>
</nav>

<?php endif ?>

