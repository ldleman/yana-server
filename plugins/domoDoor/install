<?php

global $myUser;
require_once('DoorAccess.class.php');
require_once('DoorLog.class.php');
$entity = new DoorLog();
$entity->create();

$entity = new DoorAccess();
$entity->create();

$s1 = New Section();
$s1->setLabel('door');
$s1->save();

$r1 = New Right();
$r1->setSection($s1->getId());
$r1->setRead('1');
$r1->setDelete('1');
$r1->setCreate('1');
$r1->setUpdate('1');
$r1->setRank('1');
$r1->save();

$entity->user = $myUser->getId();
$entity->code = str_pad(rand(0,1000),4, "0");
$entity->save();


?>
