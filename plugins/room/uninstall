<?php
$table = new Room();
$table->drop();

$table_section = new Section();
$id_section = $table_section->load(array("label"=>"room"))->getId();
$table_section->delete(array('label'=>'room'));

$table_right = new Right();
$table_right->delete(array('section'=>$id_section));

?>