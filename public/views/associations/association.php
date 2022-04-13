<?php if (!defined('ROOT_PATH')) exit ?>

        <tr>
            <th scope="row" id="name"><?=$association->name?>
            <td ><?=$association->address?>
            <td ><?=$association->telephone?>
            <td ><?=$association->president->realName?>
            <td ><?=count($association->partners)?>
