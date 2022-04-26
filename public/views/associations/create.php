<?php defined('ROOT_PATH') OR exit() ?>

<section id="create">

<header>
<h2>Create Yours...</h2>
</header>

<form method="post"
    action="#">
    <p><label>Association Name: <input type="text" name="create[name]" required maxlength="64" /></label></p>
    <p><label>Association nickname: <input type="text" name="create[nickname]" required minlength="8" maxlength="32" pattern="[a-zA-z_]*"/></label></p>
    <p><label>Address of the headquarters: <input type="text" name="create[address]" /></label></p>
    <fieldset>
        <legend>Phone Number</legend>
        <p>
            <label> <input type="radio" name="create[phone]" value="new" checked /> </label>&nbsp;<label>
                <select name="create[int]">
                <option value="351">+351 &#x1F1F5;&#x1F1F9;</option>
                <option value="1">+1 &#x1F1FA;&#x1F1F8;</option>
                <option value="256">+256 &#x1F1FA;&#x1F1EC;</option>
                </select>
            </label>
            <label> <input type="tel" name="create[number]" maxlength="15"/> </label>
        </p>
        <?php if (UserSession::getUser()->getTelephone()): ?>
        <p><label> <input type="radio" name="create[phone]" value="yours" /> use user's phone number </label></p>
        <?php endif ?>
    </fieldset>
    <p><label>Taxpayer number: <input type="number" name="create[taxpayerNumber]" required minlength="9" maxlength="9" /></label></p>
    <p><button>Create</button></p>
</form>

</section>
