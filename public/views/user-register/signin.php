<?php

if (!defined('ROOTPATH'))
    exit;

?>

<main>
    <section>
        <form action="#" method="post" id="sign">
            <label for="username">Username: </label>
            <input type="text" name="username" required>
            <br>
            <label for="email">e-mail: </label>
            <input type="email" name="email" required>
            <br>
            <label for="pass">senha: </label>
            <input type="password" name="pass" required>
            <br>
            <label for="verifyPass">senha(verifica&#xE7;&#xE3;o): </label>
            <input type="password" name="verifyPass" required>
            <br>
            <input type="submit" name="signin" value="Criar conta">
        </form>
    </section>
</main>
