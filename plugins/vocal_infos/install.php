<?php
require_once('VocalInfo.class.php');
$table = new VocalInfo();
$table->create();
 
$s1 = New Section();
$s1->setLabel('vocal');
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
$conf->put('plugin_vocalinfo_woeid','615702');
$conf->put('plugin_vocalinfo_place','Paris France');
?>