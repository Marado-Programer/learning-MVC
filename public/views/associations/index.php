<?php if (!defined('ROOT_PATH')) exit ?>

<main id="top">

<footer>

<ul>
    <li><a href="#search">search</a></li>
    <li><a href="#create">create</a></li>
</ul>

</footer>

<header>
<h1>Associations</h1>
</header>

<?php
if (
    UsersManager::getPermissionsManager()->checkUserPermissions(
        $this->userSession->user,
        PermissionsManager::P_VIEW_ASSOCIATIONS,
        false
    )
)
    require VIEWS_PATH . '/associations/search.php';

if (
    UsersManager::getPermissionsManager()->checkUserPermissions(
        $this->userSession->user,
        PermissionsManager::P_VIEW_ASSOCIATIONS,
        false
    )
)
    require VIEWS_PATH . '/associations/create.php';
?>

<footer>

<p><a href="#top">Go to the top</a></p>

</footer>

</main>
