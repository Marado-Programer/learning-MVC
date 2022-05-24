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

if ($pm->checkPermissions(
    $permissions,
    PermissionsManager::AP_ADMNI_PARTNERS,
    false
)): ?>
<p>User Premissions</p>
<form method="post"
    action="#">
<?php $pNames = [
    'Partner',
    'Admnistrator',
    'Create News',
    'Publish News',
    'Edit News',
    'Delete News',
    'Create Events',
    'Enter Events',
    'Edit Events',
    'Delete Events',
    'Create Images',
    'Add Images',
    'Remove Images',
    'Edit Images',
    'Delete Images',
    'Create Image Galleries',
    'Edit Image Galleries',
    'Delete Image Galleries',
    'Partners Admnistrator',
    'Association Admnistrator',
    'President'
]; ?>
<table>
<tr>
    <th> Username
    <?php for ($i = 1, $j = 0; $i < PermissionsManager::AP_PRESIDENT; $i <<= 1, $j++): ?>
    <th> <?=$pNames[$j]?>
    <?php endfor ?>
<?php foreach ($this->association->getPartners() as $partner): ?>
<?php $p = $this->model->userAdmniPermissions($partner, $this->association); ?>
<tr>
    <td> <?=$partner->username?><input type="hidden" name="users[p][<?=$partner->getID()?>][0]" />
    <?php for ($i = 1, $j = 0; $i <= PermissionsManager::AP_PRESIDENT; $i <<= 1, $j++): ?>
<?php
    $radio = false;
    $check = false;
    if ($pm->checkPermissions($p, $i, false)) $check = true;
    if (20 == $j) $radio = true;
    ?>
        <th> <?php if ($radio): ?> <input type="radio" name="users[president]" value="<?=$partner->getID()?>"<?=$check ? ' checked' : ''?> /> <?php else: ?> <input type="checkbox" name="users[p][<?=$partner->getID()?>][<?=0x1 << $j?>]"<?=$check ? ' checked' : ''?> /> <?php endif ?>
    <?php endfor ?>
<?php endforeach ?>
</table>
<button type="submit" name="users[change]">Change Premissions</button>
<?php endif ?>
</form>
</main>

