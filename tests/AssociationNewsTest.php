<?php

/**
 * 
 */

require_once '../src/classes/User.php';
require_once '../src/classes/Partner.php';
require_once '../src/classes/President.php';
require_once '../src/classes/Association.php';
require_once '../src/classes/News.php';

$u1 = new User();

$u1->createAssociation('authntic games', 'Rua bem fixe ngl', 351, 956433278, 123654344);

echo $u1->yourAssociations[0]->listNewsSimplified();

$association->addNews(new News($association, 'Test Title', 'Teste noticia corpo bem fichex'));

$news1 = new News($association, 'Test 2 de news', 'Teste bem fichex');
$news2 = new News($association, 'amae', 'corpo bem fichex');
$association->addNews($news1);
$association->addNews($news2);

echo $association->listNewsSimplified();

$association->deleteNews(1);

echo $association->listNewsSimplified();

$news3 = new News($association, 'ok veryu good', 'lorem ipsum');
$news4 = new News($association, 'sadfhkujgls', 'corsdfg');
$association->addNews($news3);
$association->addNews($news4);

echo $association->listNews(false);

echo $association->listNewsById(1);

