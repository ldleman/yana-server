<?php
require_once('Door.class.php');
$table = new Door();
$table->create();

$s1 = New Section();
$s1->setLabel('porte');
$s1->save();

$r1 = New Right();
$r1->setSection($s1->getId());
$r1->setRead('1');
$r1->setDelete('1');
$r1->setCreate('1');
$r1->setUpdate('1');
$r1->setRank('1');
$r1->save();


?>