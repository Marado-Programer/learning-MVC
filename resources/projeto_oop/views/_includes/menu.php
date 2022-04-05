<?php if (!defined('ABSPATH')) exit; ?>

<?php
if ($this->login_required && !$this->logged_in)
    return;
if ($this->logged_in) {
    echo "BemVindo: " . $this->user_name;
    ?>
    <a href="<?php echo HOME_URI; ?>/login/delete/">Logout</a>
    <?php } else {
    ?>
    <a href="<?php echo HOME_URI; ?>login/">Login</a>
    <?php
}
?>

<nav class="menu clearfix">
    <ul>
        <li><a href="<?php echo HOME_URI; ?>">Home</a></li>

        <li><a href="<?php echo HOME_URI; ?>/user-register/">User Register</a></li>
        <li><a href="<?php echo HOME_URI; ?>/noticias/">Notícias</a></li>
        <li><a href="<?php echo HOME_URI; ?>/noticias/adm/">Notícias Admin</a></li>
        <li><a href="<?php echo HOME_URI; ?>/projetos/">Projetos</a></li>
        <li><a href="<?php echo HOME_URI; ?>/projetos/adm/">Projetos list</a></li>
    </ul>
</nav>