<?php
require_once('Room.class.php');
$room = new Room();
$room->create();

$room->setName('Cuisine');
$room->setDescription('L\'endroit oû l\'on dégomme les coockies');
$room->save();

$room = new Room();
$room->setName('Salon');
$room->setDescription('Parfait pour prendre l\'apéro...');
$room->save();

?>