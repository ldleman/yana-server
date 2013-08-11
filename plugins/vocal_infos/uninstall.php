<?php
require_once('VocalInfo.class.php');
$table = new VocalInfo();
$table->drop();
 
$table_configuration = new configuration();
$table_configuration->delete(array('key'=>'plugin_vocalinfo_woeid'));
$table_configuration->delete(array('key'=>'plugin_vocalinfo_place'));
/*
$table_section = new Section();
$id_section = $table_section->load(array("label"=>"vocal"))->getId();
$table_section->delete(array('label'=>'vocal'));
 
$table_right = new Right();
$table_right->delete(array('section'=>$id_section));
 */
?>