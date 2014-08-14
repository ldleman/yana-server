<?php
require_once('RadioRelay.class.php');
$table = new RadioRelay();
$table->create();

$s1 = New Section();
$s1->setLabel('radio relais');
$s1->save();

$r1 = New Right();
$r1->setSection($s1->getId());
$r1->setRead('1');
$r1->setDelete('1');
$r1->setCreate('1');
$r1->setUpdate('1');
$r1->setRank('1');
$r1->save();

$conf = new Configuration();
$conf->put('plugin_radioRelay_emitter_pin','0');
$conf->put('plugin_radioRelay_emitter_code','8217034');

?>