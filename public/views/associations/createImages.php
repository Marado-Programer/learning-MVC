<?php if (!defined('ROOT_PATH')) exit ?>

<section id="create-image">

<header>
<h3>Create Image</h3>
</header>

<?php
/**
 * The corrected user input and the errors that were made on the admni model.
 */
if (isset($_SESSION['image'])) {
    $create = unserialize($_SESSION['image']);
    unset($_SESSION['image']);
} else
    $create = [];

if (isset($_SESSION['image-errors'])) {
    $errors = $_SESSION['image-errors'];
    unset($_SESSION['image-errors']);
} else
    $errors = [];

if (isset($_SESSION['image-created'])) {
    $created = $_SESSION['image-created'];
    unset($_SESSION['image-created']);
} else
    $created = "\0";
?>

<form method="post"
    action="#"
    enctype="multipart/form-data">
    <p><label>Image title: <input type="text" name="image[title]" maxlength="80" minlength="1" required size="80" /></label></p>
    <p><label>Image: <input type="file" name="image-image" accept="image/*" required /></label></p>
    <p><button>Create</button></p>
</form>

<?php unset($event) ?>

<aside>
<?php if (!empty($imageCreated)): ?>
<h4>It Worked!</h4>
<p><?=$imageCreated?></p>
<?php endif ?>
<?php unset($imageCreated); ?>
<?php if (!empty($imageErrors)): ?>
<h4>Errors Found</h4>
<?php foreach ($imageErrors as $error): ?>
<p><?=$error?></p>
<?php endforeach ?>
<?php endif ?>
<?php unset($imageErrors); ?>
</aside>

</section>
