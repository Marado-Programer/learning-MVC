<?php if (!defined('ROOT_PATH')) exit ?>

<main>

<header>
<h1>Association Admnistrator Panel</h1>
</header>

<?php
$pm = UsersManager::getTools()->getPremissionsManager();

if ($pm->checkPermissions(
    $permissions,
    PermissionsManager::AP_ADMNI_NEWS,
    false
)) {
    require VIEWS_PATH . '/associations/news.php';
}

if ($pm->checkPermissions(
    $permissions,
    PermissionsManager::AP_ADMNI_EVENTS,
    false
))
    require VIEWS_PATH . '/associations/events.php';

if ($pm->checkPermissions(
    $permissions,
    PermissionsManager::AP_ADMNI_IMAGES,
    false
))
    require VIEWS_PATH . '/associations/images.php';

if ($this->association->president == UserSession::getUser())
    echo dechex(PermissionsManager::AP_PRESIDENT); 
?>
</main>
