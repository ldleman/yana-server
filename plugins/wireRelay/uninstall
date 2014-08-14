<?php
$table = new WireRelay();
$table->drop();

$table_section = new Section();
$id_section = $table_section->load(array("label"=>"relais filaire"))->getId();
$table_section->delete(array('label'=>'relais filaire'));

$table_right = new Right();
$table_right->delete(array('section'=>$id_section));

?>