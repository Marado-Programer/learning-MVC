<?php if (!defined('ROOT_PATH')) exit ?>

        <tr>
            <th scope="row"><?=$association->name?>
            <td ><?=$association->address?>
            <td ><?=$association->telephone?>
            <td ><?=$association->partners['president']->realName?>
            <td ><?=count($association->partners)?>
            <form method="post"
                action="#">
            <td class="space"><p><input type="hidden" name="association[name]" value="<?=$association->nickname?>" /></p>
            <td class="actions"><p><button name="association[redirect]" value="page">Visit page</button><?php if (!$iterator->getSawUsers()): ?><br />
            <button name="association[redirect]" value="admin">Admnistrator Panel</button><?php endif ?></p>
            </form>
