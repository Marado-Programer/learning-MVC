<?php if (!defined('ROOT_PATH')) exit ?>

<section id="create-event">

<header>
<h3>Create Event</h3>
</header>

<?php
/**
 * The corrected user input and the errors that were made on the admni model.
 */
if (isset($_SESSION['event'])) {
    $event = unserialize($_SESSION['event']);
    unset($_SESSION['event']);
} else
    $event = [];

if (isset($_SESSION['event-errors'])) {
    $eventErrors = $_SESSION['event-errors'];
    unset($_SESSION['event-errors']);
} else
    $eventErrors = [];

if (isset($_SESSION['event-created'])) {
    $eventCreated = $_SESSION['event-created'];
    unset($_SESSION['event-created']);
} else
    $eventCreated = "\0";
?>

<form method="post"
    action="#"
    enctype="multipart/form-data">
    <p><label>Event title: <input type="text" name="event[title]" value="<?php
        if (isset($event['title']))
            echo htmlspecialchars($event['title'], ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    ?>" maxlength="80" minlength="1" required size="80" /></label></p>
    <p><label>Description: <textarea autocomplete="on" cols="140" rows="4" minlength="1" maxlength="280" placeholder="Event description..." name="event[description]" required><?php
        if (isset($event['description']))
            echo htmlentities($event['description']);
    ?></textarea></label></p>
    <p><label> <input type="datetime-local" name="event[endDate]" value="<?php
        if (isset($event['endDate']))
            echo $event['endDate']->format('Y-m-d\TH:i');
    ?>" min="<?=(new DateTime)->format('Y-m-d\TH:i')?>" required /></label></p>
    <p><button>Create</button></p>
</form>

<?php unset($event) ?>

<aside>
<?php if (!empty($eventCreated)): ?>
<h4>It Worked!</h4>
<p><?=$eventCreated?></p>
<?php endif ?>
<?php unset($eventCreated); ?>
<?php if (!empty($eventErrors)): ?>
<h4>Errors Found</h4>
<?php foreach ($eventErrors as $error): ?>
<p><?=$error?></p>
<?php endforeach ?>
<?php endif ?>
<?php unset($eventErrors); ?>
</aside>

</section>
