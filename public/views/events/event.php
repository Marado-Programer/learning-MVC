<?php
    defined('ROOT_PATH') OR exit();

    $button = "";
    $aAttrs = "";
    if (isset($_POST['search-events'])) {
        $button = '<p><button name="asAssociation" value="' . $_POST['search-events'] . '">Make part of this Event</button></p>';
        $aAttrs = ' target="_blank" rel="noopener noreferrer"';
    } elseif ($this->user->isLoggedIn())
        foreach ($event->associations as $association)
            if (in_array($this->user, $association->getPartners()))
                if (empty($event->registrations))
                    $button = '<p><button name="asPartner">Buy ticket</button></p>';
                foreach ($event->registrations as $registration) {
                    if ($registration->getIdPartner() != $this->user->getID()) {
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

