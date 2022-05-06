<?php

defined('ROOT_PATH') OR exit();

if ($this->loginRequired && !$this->user->isLoggedIn())
    $this->tools->getRedirect()->redirect(HOME_URI . '/login');

?>

<!DOCTYPE html>

<html lang="en">

<head>
    <title><?=$this->title?></title>
    <link href="<?=STYLE_URI?>/css/style.css" rel="stylesheet" title="main CSS" />
    <link href="https://github.com/Marado-Programmer/learning-MVC" rel="author" />
    <meta charset="utf-8" />
    <meta name="author" content="Marado-Programmer" />
    <meta name="description" content="An school project to learn how to use the MVC" />
    <meta name="keywords" content="php,MVC,programming,learning,learn,backend" />
    <meta name="theme-color" content="#2166ac" />
    <meta name="color-scheme" content="light" />
    <meta http-equiv="default-style" content="main CSS" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <script src="<?=SCRIPT_URI?>/java-script/getEventsNotifiers.js"></script>
</head>
