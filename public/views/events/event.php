<?php
    defined('ROOT_PATH') OR exit();
    $button = "\0";
    $aAttrs = "\0";
    if (isset($_POST['search-events'])) {
        $button = '<p><button name="asAssociation" value="' . $_POST['search-events'] . '">Make part of this Event</button></p>';
        $aAttrs = ' target="_blank" rel="noopener noreferrer"';
    } elseif (UserSession::getUser()->loggedIn)
        foreach ($event->associations as $association)
            if (in_array(clone UserSession::getUser(), $association->partners))
                $button = '<p><button name="asPartner">Buy ticket</button></p>';
?>
<li>
    <form method="post"
        action="#"
        enctype="multipart/form-data">
        <?=$event?>
        <?=$button?>
        <p>Event created by <a href="<?=HOME_URI?>/@<?=$event->associations['ini']->nickname?>"<?=$aAttrs?>><?=$event->associations['ini']->name?></a></p>
        <p><input type="hidden" name="event[id]" value="<?=$event->id?>" /></p>
    </form>
    <hr />
</li>

