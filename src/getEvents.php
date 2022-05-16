<?php

/**
 *
 */

header('Content-type: application/json');

include_once '../config/URIconfig.php';
include_once '../config/DBconfig.php';
include_once './global/global-functions.php';

$user = checkArray($_GET, 'userID');
$list = [];

try {
    $events = new EventsList();
    $db = new DBConnection();
    $instancer = Instanceator::getInstanceator($db);
    $db->checkAccess();

    $registrations = $instancer->instanceRegistrationsByPartnerID($user) ?? [];

    foreach ($registrations as $registration)
        $events->add($registration->getEvent());

    global $list;
    $today = new DateTime();
    $iterator = $events->getIterator(EventsList::$END_FIRST_ORDER);
    while ($iterator->valid()) {
        $event = $iterator->current();
        $date = $event->endDate;
        if ($date > $today)
            $list[] = ['title' => $event->title, 'description' => $event->description, 'dateString' => $date->format('Y-m-d\TH:i:s')];
        $iterator->next();
    }
} catch (Exception $e) {
    echo $e->getMessage();
} finally {
    global $list;
    echo json_encode($list);
}

