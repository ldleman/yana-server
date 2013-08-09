<?php
require_once('RadioRelay.class.php');
$table = new RadioRelay();
$table->drop();

$conf = new Configuration();
$conf->delete(array('key'=>'plugin_radioRelay_emitter_pin'));
$conf->delete(array('key'=>'plugin_radioRelay_emitter_code'));
?>