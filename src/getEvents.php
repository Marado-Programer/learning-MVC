<?php

/**
 *
 */

header('Content-type: application/json');

include_once '../config/config.php';
include_once './global/global-functions.php';

$user = checkArray($_GET, 'userID');

try {
    $events = new EventsList();
    $db = new DBConnection();
    $instancer = Instanceator::getInstanceator($db);
    $db->checkAccess();

    foreach ($instancer->instanceRegistrationsByPartnerID($user) as $registration)
        $events->add($registration->getEvent());
} catch (Exception $e) {
    echo $e->getMessage();
} finally {
    $list = [];
    $today = new DateTime();
    $iterator = $events->getIterator(EventsList::$END_FIRST_ORDER);
    while ($iterator->valid()) {
        $event = $iterator->current();
        $date = $event->endDate;
        if ($date > $today)
            $list[] = ['title' => $event->title, 'description' => $event->description, 'dateString' => $date->format('Y-m-d\TH:i:s')];
        $iterator->next();
    }
    echo json_encode($list);
}

