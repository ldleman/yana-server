<?php
require_once('Room.class.php');
$room = new Room();
$room->create();

$s1 = New Section();
$s1->setLabel('room');
$s1->save();

$r1 = New Right();
$r1->setSection($s1->getId());
$r1->setRead('1');
$r1->setDelete('1');
$r1->setCreate('1');
$r1->setUpdate('1');
$r1->setRank('1');
$r1->save();

$room->setName('Cuisine');
$room->setDescription('L\'endroit oû l\'on dégomme les cookies');
$room->save();

$room = new Room();
$room->setName('Salon');
$room->setDescription('Parfait pour prendre l\'apéro...');
$room->save();

?>
