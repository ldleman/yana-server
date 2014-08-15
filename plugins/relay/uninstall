<?php
require_once('RadioRelay.class.php');
$table = new RadioRelay();
$table->drop();

$conf = new Configuration();
$conf->delete(array('key'=>'plugin_radioRelay_emitter_pin'));
$conf->delete(array('key'=>'plugin_radioRelay_emitter_code'));

$table_section = new Section();
$id_section = $table_section->load(array("label"=>"radio relais"))->getId();
$table_section->delete(array('label'=>'radio relais'));

$table_right = new Right();
$table_right->delete(array('section'=>$id_section));

?>
