<?php
    defined('ROOT_PATH') OR exit();
    $button = "\0";
    $aAttrs = "\0";
    if (isset($_POST['search-events'])) {
        $button = '<p><button name="asAssociation" value="' . $_POST['search-events'] . '">Make part of this Event</button></p>';
        $aAttrs = ' target="_blank" rel="noopener noreferrer"';
    } elseif (UserSession::getUser()->isLoggedIn())
        foreach ($event->associations as $association)
            if (in_array(UserSession::getUser(), $association->getPartners()))
                foreach ($event->registrations as $registration) {
                    if ($registration->getIdPartner() != UserSession::getUser()->getID()) {
                       $button = '<p><button name="asPartner">Buy ticket</button></p>';

                        break;
                    }
                }
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

