<?php
require_once('WireRelay.class.php');
$table = new WireRelay();
$table->create();

$s1 = New Section();
$s1->setLabel('relais filaire');
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