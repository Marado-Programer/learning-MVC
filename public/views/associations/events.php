<?php defined('ROOT_PATH') OR exit() ?>

<section>

<header>
<h2>Events</h2>
</header>

<?php
if (UsersManager::getPermissionsManager()->checkPermissions(
    $permissions,
    PermissionsManager::AP_CREATE_EVENTS,
    false
))
    require VIEWS_PATH . '/associations/createEvents.php';
?>

<?php
if (UsersManager::getPermissionsManager()->checkPermissions(
    $permissions,
    PermissionsManager::AP_ENTER_EVENTS,
    false
))
    require VIEWS_PATH . '/associations/enterEvents.php';
?>

</section>
