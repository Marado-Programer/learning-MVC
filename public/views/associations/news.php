<?php defined('ROOT_PATH') OR exit() ?>

<section>

<header>
<h2>News</h2>
</header>

<?php
if (!UsersManager::getTools()->getPremissionsManager()->checkPermissions(
    $permissions,
    PermissionsManager::AP_CREATE_NEWS,
    false
))
    require VIEWS_PATH . '/associations/createNews.php';
?>

</section>

