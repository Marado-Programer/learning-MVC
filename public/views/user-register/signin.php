<?php if (!defined('ROOT_PATH')) exit() ?>

<main>
    <section>
        <header>
            <h1>Sign In</h1>
        </header>
        <form action="#" method="post">
            <label for="username">username*: </label>
            <input type="text" name="register[username]" required \>
            <br \>
            <label for="username">real name: </label>
            <input type="text" name="register[realName]" \>
            <br \>
            <label for="email">e-mail*: </label>
            <input type="email" name="register[email]" required \>
            <br \>
            <label for="pass">password*: </label>
            <input type="password" name="register[password]" required \>
            <br \>
            <label for="verifyPass">password(verify)*: </label>
            <input type="password" name="register[verify-password]" required \>
            <br \>
            <input type="submit" name="create" value="Create Account" \>
        </form>
    </section>
</main>
