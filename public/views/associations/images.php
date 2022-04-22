<?php defined('ROOT_PATH') OR exit() ?>

<section>

<header>
<h2>Images</h2>
</header>

<?php
if (UsersManager::getTools()->getPermissionsManager()->checkPermissions(
    $permissions,
    PermissionsManager::AP_CREATE_IMAGES,
    false
))
    require VIEWS_PATH . '/associations/createImages.php';
?>

</section>
