<?php if (!defined('ROOT_PATH')) exit() ?>

<main>
    <section>
        <header>
            <h1>Sign Up</h1>
        </header>
        <link href="<?=STYLE_URI?>/css/sign-up.css" rel="stylesheet" />
        <form accept-charset="UTF-8"
            method="post"
            action="#"
            enctype="multipart/form-data">
            <p><label><span class="required">Username</span>: <input type="text" name="register[username]" maxlength="32" minlength="4" pattern="^[\w_]{4, 32}$" required size="32" \></label></p>
            <p><label>Real name: <input type="text" name="register[realName]" \></label></p>
            <p><label><span class="required">e-mail</span>: <input type="email" name="register[email]" maxlength="320" minlength="3" pattern="^(([^&lt;&gt;()\[\]\\.,;:\s@&quot;]+(\.[^&lt;&gt;()\[\]\\.,;:\s@&quot;]+)*)|(&quot;.+&quot;))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$" required size="64" \></label></p>
            <fieldset>
                <legend>Telephone:</legend>
                <p>
                    <label>International call prefix:
                        <select name="register[int]">
                        <option value="351">+351 &#x1F1F5;&#x1F1F9;</option>
                        <option value="1">+1 &#x1F1FA;&#x1F1F8;</option>
                        <option value="256">+256 &#x1F1FA;&#x1F1EC;</option>
                        </select>
                    </label>
                    <label>Number: <input type="tel" name="register[number]" maxlength="15" minlength="1" pattern="^\d{3,15}$" size="15"/> </label>
                </p>
            </fieldset>
            <p><label><span class="required">Password</span>: <input type="password" name="register[password]" minlength="4" required \></label></p>
            <p><label><span class="required">Password(verify)</span>: <input type="password" name="register[verify-password]" minlength="4" required \></label></p>
            
            <p><button type="submit" name="create">Sign In</button></p>
        </form>
        
        <aside><p><small><span class="required-meaning"><span class="required-sign">*</span> means required</span></small></p></aside>

        <footer>
            <p><a href="<?=HOME_URI?>/login">Already have an account?</a></p>
        </footer>
    </section>
</main>
