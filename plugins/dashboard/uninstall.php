<?php

require_once('Widget.class.php');

$table = new Widget();
$table->drop();


$table_section = new Section();
$id_section = $table_section->load(array("label"=>"dashboard"))->getId();
$table_section->delete(array('label'=>'dashboard'));

$table_right = new Right();
$table_right->delete(array('section'=>$id_section));

?>