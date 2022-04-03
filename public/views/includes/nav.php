<?php

if (!defined('ROOT_PATH'))
    exit;

if ($this->loginRequired && !$this->loggedIn)
    return;

if ($this->loggedIn) {
?>
    <p>Welcome <?=$this->userName?></p>
    <a href="<?=HOME_URI?>/login/delete/">Logout</a>
<?php
} else
?>
    <a href="<?=HOME_URI?>/login/">Login</a>
    <ul>
        <li><a href="<?=HOME_URI?>">Home</a></li>
        <li><a href="<?=HOME_URI?>/user-register">User register</a></li>
        <li><a href="<?=HOME_URI?>/noticias">Noticias</a></li>
        <li><a href="<?=HOME_URI?>/noticias/adm">Gerir Noticias</a></li>
        <li><a href="<?=HOME_URI?>/projetos">Projetos</a></li>
        <li><a href="<?=HOME_URI?>/projetos/adm">Gerir Projetos</a></li>
    </ul>
</nav>
