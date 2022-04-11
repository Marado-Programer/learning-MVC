<main>

    <header>
    <h1>Associations</h1>
    </header>

    <section id="create">

    <header>
    <h2>Create Yours...</h2>
    </header>

    <form method="post"
        action="#">
        <p><label>Association Name: <input type="text" name="create[name]" required /></label></p>
        <p><label>Address of the headquarters: <input type="text" name="create[address]" /></label></p>
        <fieldset>
            <legend>Phone Number</legend>
            <p>
                <label> <input type="radio" name="create[phone]" /> </label>
                <label>
                    <select name="create[int]">
                    <option value="351">+351 &#x1F1F5;&#x1F1F9;</option>
                    <option value="1">+1 &#x1F1FA;&#x1F1F8;</option>
                    <option value="256">+256 &#x1F1FA;&#x1F1EC;</option>
                    </select>
                </label>
                <label> <input type="tel" name="create[number]" /> </label>
            </p>
            <p><label> <input type="radio" name="create[phone]" /> use user's phone number </label></p>
        </fieldset>
        <p><label>Taxpayer number: <input type="number" name="create[taxpayerNumber]" /></label></p>
        <p><button>Create</button></p>
    </form>

    </section>

    <footer>

    <ul>
        <li><a href="#create">create</a></li>
    </ul>

    </footer>

</main>
