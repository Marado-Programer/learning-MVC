<?php if (!defined('ROOT_PATH')) exit ?>

        <tr>
            <th scope="row" id="name"><?=$association->name?>
            <td ><?=$association->address?>
            <td ><?=$association->telephone?>
            <td ><?=$association->president->realName?>
            <td ><?=count($association->partners)?>
            <td class="space"><p><input type="hidden" form="visit" name="association[name]" value="<?=$association->nickname?>" /</p>
            <td class="actions"><p><button form="visit" name="association[redirect]" value="page">Visit page</button>
