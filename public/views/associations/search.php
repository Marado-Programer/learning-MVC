<?php if (!defined('ROOT_PATH')) exit ?>

<section id="search">

<header>
<h2>Search</h2>
</header>

<article>

<link href="<?=HOME_URI?>/public/style/css/associations.css" rel="stylesheet" />

<form method="post"
    action="#"
    id="visit">
</form>

<table>
    <caption>
        <strong>Associations List</strong>
        <details>
            <summary>Filters</summary>
            <p>Normal</p>
        </details>
    </caption>
    <colgroup> <col> <col> <col>
    <colgroup> <col>
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
    $iterator = $this->associations->getIterator(AssociationsList::$DEFAULT_ORDER);
    while ($iterator->valid()) {
        $association = $iterator->current();
        require VIEWS_PATH . '/associations/association.php';
        $iterator->next();
    }
?>
</table>

</article>

</section>
