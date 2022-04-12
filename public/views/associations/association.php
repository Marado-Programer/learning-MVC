<?php if (!defined('ROOT_PATH')) exit ?>

        <tr />
            <td /><?=$association->name?>
            <td /><?=$association->address?>
            <td /><?=$association->telephone?>
            <td /><?=$association->president->realName?>
            <td /><?=count($association->partners)?>
