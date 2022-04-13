<?php if (!defined('ROOT_PATH')) exit ?>

<section id="create">

<header>
<h3>Create News</h3>
</header>

<form method="post"
    action="#">
    <p><label>News title: <input type="text" name="create[title]" required /></label></p>
    <p><label>Image: <input type="file" name="create[image]" /></label></p>
    <p><label>Article: <textarea name="create[article]"></textarea></label></p>
    <p><button>Create</button></p>
</form>

</section>
