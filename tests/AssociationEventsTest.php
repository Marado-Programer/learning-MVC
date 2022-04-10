<?php

/**
 * 
 */

require_once '../src/classes/User.php';
require_once '../src/classes/Association.php';
require_once '../src/classes/Events.php';

$u1 = new User();

$a1 = new Association('Empresa do Jonhy', 'Rua do Sabao', 43, 347328884, 123895234, $u1);

$a1->createEvent('Evento bem pog', 'vai ser muito pog');

$a2 = new Association('Empresa muito fixe do Torres', 'Rua ok', 54, 312348584, 123895234, $u1);

echo $a1;

echo $a2;

echo $a1->listEvents();

$a2->useEvent($a1->events[0]);

echo $a1->listEvents();
