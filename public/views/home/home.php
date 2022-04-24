<?php if (!defined('ROOT_PATH')) exit ?>

<header>
    <h1>Welcome Home!</h1>
</header>

<?php if (UserSession::getUser()->isLoggedIn()): ?>

<section id="search">

<header>
<h2>Your Associations</h2>
</header>

<article>

<link href="<?=HOME_URI?>/public/style/css/associations.css" rel="stylesheet" />

<table>
    <caption>
        <strong>Associations List</strong>
        <details>
            <summary>Filters</summary>
            <p>Normal</p>
        </details>
    </caption>
    <colgroup> <col> <col> <col> <col>
    <colgroup> <col> <col>
    <thead>
        <tr>
            <th rowspan="2" scope="col" id="name">Name
            <th colspan="2" scope="colgroup" id="contacts">Contacts
            <th rowspan="2" scope="col">President
            <th rowspan="2" scope="col">Number of Partners
            <th rowspan="2" class="space">
            <th rowspan="2" scope="col" class="actions">Actions
        <tr>
            <th headers="contacts" scope="col">Address
            <th headers="contacts" scope="col">Telephone
    <tbody>
<?php
    $iterator = $this->userAssociations->getIterator(AssociationsList::$USERS_FIRST_ORDER);
    while ($iterator->valid()) {
        $association = $iterator->current();
        require VIEWS_PATH . '/home/association.php';
        $iterator->next();
    }
?>
</table>
<footer>
<p><a href="<?=HOME_URI?>/associations#create">Create an Association!</a></p>
</footer>

</article>

</section>

<?php endif ?>

<?php
if (UserSession::getUser() instanceof Partner)
    require VIEWS_PATH . '/home/createNews.php';
?>

