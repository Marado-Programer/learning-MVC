<?php if (!defined('ROOT_PATH')) exit ?>

<section id="create">

<header>
<h3>Create News</h3>
</header>

<?php $create = $_POST['create'] ?? [] ?>

<form method="post"
    action="#"
    enctype="multipart/formdata">
    <p><label>News title: <input type="text" name="create[title]" value="<?php
        if (isset($create['title']))
            echo strip_tags($create['title']);
    ?>" required /></label></p>
    <p><label>Image: <input type="file" name="create[image]" accept="image/*" required /></label></p>
    <p><label>Article: <textarea name="create[article]" required><?php
        if (isset($create['article']))
            echo $create['article'];
    ?></textarea></label></p>
    <p><button>Create</button></p>
</form>

</section>
