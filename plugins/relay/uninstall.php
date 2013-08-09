<?php
$table = new RadioRelay();
$table->drop();

$conf = new Configuration();
$conf->delete('plugin_radioRelay_emitter_pin');
$conf->delete('plugin_radioRelay_emitter_code');
?>