<?php if (!defined('ROOT_PATH')) exit() ?>

<?php $values = $_SESSION['sign-up-values'] ?? []; ?>

<main>
    <section id="sign-up">
        <div id="sign-up-main">
        <header>
            <h1>Sign Up</h1>
        </header>
        <link href="<?=STYLE_URI?>/css/sign-up.css" rel="stylesheet" />
        <form accept-charset="UTF-8"
            method="post"
            id="sign-up-form">
            <p><label><span class="required">Username</span>: <input type="text" name="register[username]" maxlength="32" minlength="4" required size="32" value="<?=htmlspecialchars(checkArray($values, 'username'))?>" \></label></p>
            <p><label>Real name: <input type="text" name="register[realName]" value="<?=htmlspecialchars(checkArray($values, 'realName'))?>" \></label></p>
            <p><label><span class="required">e-mail</span>: <input type="email" name="register[email]" maxlength="320" minlength="3" required size="64" value="<?=htmlspecialchars(checkArray($values, 'email'))?> "\></label></p>
            <fieldset id="form-telephone-field">
                <legend>Telephone:</legend>
                <p>
                    <label>International call prefix:
                        <select name="register[int]" value="<?=checkArray($values, 'int')?>">
                        <option value="351">+351 &#x1F1F5;&#x1F1F9;</option>
                        <option value="1">+1 &#x1F1FA;&#x1F1F8;</option>
                        <option value="256">+256 &#x1F1FA;&#x1F1EC;</option>
                        </select>
                    </label>
                    <label>Number: <input type="tel" name="register[number]" maxlength="15" minlength="1" size="15" value="<?=htmlspecialchars(checkArray($values, 'number'))?>"/> </label>
                </p>
            </fieldset>
            <p><label><span class="required">Password</span>: <input type="password" name="register[password]" minlength="4" required \></label></p>
            <p><label><span class="required">Password(verify)</span>: <input type="password" name="register[verify-password]" minlength="4" required \></label></p>
            
            <p><button type="submit" name="create">Sign In</button></p>
        </form>
        </div>
        
        <aside id="sign-up-aside">
            <div>
            <p><small><span class="required-meaning"><span class="required-sign">*</span> means required</span></small></p>
            </div>
            <?php if (isset($_SESSION['sign-up-errors']) && count($_SESSION['sign-up-errors']) > 0): ?>
            <div>
            <h2 id="error-title">Errors</h2>
            <ol>
                <?php foreach ($_SESSION['sign-up-errors'] as $error): ?>
                <li><p class="error"><?=$error?></p></li>
                <?php endforeach ?>
            </ol>
            </div>
            <?php endif; unset($_SESSION['sign-up-errors']); ?>
            <?php if (isset($_SESSION['sign-up-succeed']) && $_SESSION['sign-up-succeed'] === true): ?>
            <div>
            <h2 id="succeed-title">Yeah!</h2>
                <p class="succeed">Your account was created.</p>
            </div>
            <?php endif; unset($_SESSION['sign-up-succeed']); ?>
        </aside>

        <footer id="sign-up-footer">
            <p><a href="<?=HOME_URI?>/login">Already have an account?</a></p>
        </footer>
    </section>
</main>

<?php unset($_SESSION['sign-up-errors'], $_SESSION['sign-up-values']) ?>
