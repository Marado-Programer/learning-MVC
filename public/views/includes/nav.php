<?php

if (!defined('ROOTPATH'))
    exit;

if ($this->loginRequired && !$this->loggedIn)
    return;

if ($this->loggedIn):
?>

    <p>Welcome <?=$this->userName?></p>
    <a href="<?=HOME_URI?>/login/delete/">Logout</a>

<?php
    else:
?>

    <a href="<?=HOME_URI?>/login/">Login</a>
    <ul>
    </ul>

<?php
    endif;
?>

</nav>
