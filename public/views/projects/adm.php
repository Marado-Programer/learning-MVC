<?php if (!defined('ROOT_PATH')) exit ?>

<?php
$adm_uri = ROOT_PATH . '/Projects/adm/';
$edit_uri = $adm_uri . 'edit/';
$delete_uri = $adm_uri . 'delete/';
$this->model->getProjects();
$this->model->insertProject();
$this->model->formConfirm = $this->model->removeProject();
$this->model->noLimit = false;
?>

<form method="post" action="" enctype="multipart/form-data">
    <label>Description</label>
    <input type="text" name="description" value="<?=htmlentities(checkArray($this->model->formData, 'description'))?>" />
    <label>Image</label>
    <input type="file" name="image" value="" />
    <label>Date</label>
    <input type="text" name="execution-date" value="<?php
    $date = checkArray($this->model->formData, 'executionDate');
    if ($date && $date != '0000-00-00 00:00:00')
        echo date('d-m-Y H:i:s', strtotime($data));
    ?>" />
    <table>
        <tr>
            <td colspan="2"><?=$this->model->formMsg?><input type="submit" value="Save" /><a href="<?=ROOT_PATH?>/Projects/adm">New Project</a></td>
        </tr>
        Projects List
        <?php foreach ($this->model->getProjects() as $project): ?>
        <tr>
            <td><a href="<?=ROOT_PATH?>/Projects/index/<?=$project['id']?>"><?=$project['id']?></a>
            <td><?=$project['description']?></td>
            <td><?=$project['executionDate']?></td>
            <td><?=$project['hyperlink']?></td>
            <td><img src="<?=UPLOAD_PATH?><?=$project['image']?>" width="30px" /></td>
            <td><a href="<?=$edit_uri . $project['id']?>">Edit</a>&nbsp;<a href="<?=$delete_uri . $project['id']?>">Delete</a></td>
        </tr>
        <?php endforeach ?>
    </table>
</form>

