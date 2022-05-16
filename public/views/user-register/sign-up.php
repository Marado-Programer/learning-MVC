<?php
defined('ROOT_PATH') OR exit();
$values = $_SESSION['sign-up-values'] ?? [];
?>

<main>

<link href="<?=STYLE_URI?>/css/sign-up.css" rel="stylesheet" type="text/css" />

<section id="sign-up">

    <header>
        <h1>Sign Up</h1>
    </header>

    <form accept-charset="UTF-8"
        method="post"
        id="sign-up-form">

        <p><label><span class="required">Username</span>: <input type="text" name="register[username]" maxlength="32" minlength="4" required value="<?=htmlspecialchars(checkArray($values, 'username'))?>" /></label></p>
        <p><label>Real&nbsp;name: <input type="text" name="register[realName]" maxlength="80" value="<?=htmlspecialchars(checkArray($values, 'realName'))?>" /></label></p>
        <p><label><span class="required">e-mail</span>: <input type="email" name="register[email]" maxlength="320" minlength="3" required value="<?=htmlspecialchars(checkArray($values, 'email'))?> "/></label></p>

        <fieldset>
            <legend>Telephone:</legend>
            <p>
                <input type="hidden" value="<?=checkArray($values, 'int')?>" id="int" />
                <label>International call prefix:<select name="register[int]" id="country-dial-in-codes"><?php require VIEWS_PATH . '/countryCallingCodes.html'?></select></label>

                <script>
                    var int = document.getElementById("int").value;
                    if (int !== "") {
                        var countries = Array.from(document.getElementById("country-dial-in-codes"));
                        var country = countries.find(country => country.value === int);
                        country.selected = true;
                    }
                </script>
                <label>Number: <input type="tel" name="register[number]" maxlength="15" minlength="1" value="<?=htmlspecialchars(checkArray($values, 'number'))?>"/> </label>
            </p>
        </fieldset>

        <p><label><span class="required">Password</span>: <input type="password" name="register[password]" minlength="4" required /></label></p>
        <p><label><span class="required">Password(verify)</span>: <input type="password" name="register[verify-password]" minlength="4" required /></label></p>
        
        <p><button type="submit" name="create">Sign In</button></p>

    </form>
    
    <aside>

    <div id="required-info">
    <p><small><span class="required-meaning"><span class="required"></span> means required</span></small></p>
    </div>

    <?php if (isset($_SESSION['sign-up-errors']) && count($_SESSION['sign-up-errors']) > 0): ?>

    <div>
    <h2 id="error-title">Errors</h2>
    <ol>
        <?php foreach ($_SESSION['sign-up-errors'] as $error): ?>
        <li><p class="msg error"><?=$error?></p></li>
        <?php endforeach ?>
    </ol>
    </div>

    <?php unset($_SESSION['sign-up-errors']); elseif (isset($_SESSION['sign-up-succeed']) && $_SESSION['sign-up-succeed'] === true): ?>

    <div>
    <h2 id="succeed-title">Yeah!</h2>
    <p class="msg succeed">Your account was created. <a href="<?=HOME_URI?>/login">Try to log in now!</a></p>
    </div>

    <?php endif; unset($_SESSION['sign-up-succeed']); ?>

    </aside>

    <footer>
        <p><a href="<?=HOME_URI?>/login">Already have an account?</a></p>
    </footer>

</section>

</main>

<?php unset($_SESSION['sign-up-errors'], $_SESSION['sign-up-values']) ?>
