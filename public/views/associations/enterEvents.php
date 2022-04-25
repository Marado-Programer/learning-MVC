<?php if (!defined('ROOT_PATH')) exit ?>

<section id="enter-event">

<header>
<h3>Enter Event</h3>
</header>

<form method="post"
    action="<?=HOME_URI?>/events/simple"
    enctype="multipart/form-data"
    target="events-iframe">
    <button name="search-events" value="<?=$this->association->getID();?>">Search for events</button>
</form>

<iframe srcdoc="<p>Search them</p>" name="events-iframe" width="640" height="379">
</iframe>

</section>
