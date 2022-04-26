<?php defined('ROOT_PATH') OR exit() ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?=$this->title?></title>
    <!-- <base href="<?=HOME_URI?>" target="_self" /> -->
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="http://alunos.epcc.pt/~al220007 al220007@epcc.pt">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?=HOME_URI?>/public/style/css/style.css" rel="stylesheet" />
    <script src="<?=HOME_URI?>/public/scripts/java-script/getEventsNotifiers.js"></script>
</head>

<body>
<input type="hidden" value="<?=UserSession::getUser()->getID()?>" id="idOfUser"\>
