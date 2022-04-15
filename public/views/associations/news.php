<?php if (!defined('ROOT_PATH')) exit ?>

<section>

<header>
<h2>News</h2>
</header>

<?php
if (UsersManager::getPermissionsManager()->checkPermissions(
    $permissions,
    PermissionsManager::AP_CREATE_NEWS,
    false
))
    require VIEWS_PATH . '/associations/createNews.php';
?>

</section>

