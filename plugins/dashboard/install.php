<?php

global $myUser;
require_once('Dashboard.class.php');
require_once('Widget.class.php');

$entity = new Dashboard();
$entity->create();
$entity->user = $myUser->getId();
$entity->label = "Général";
$entity->default = 1;
$entity->save();

$dashboard = $entity->id;

$entity = new Widget();
$entity->create();


$s1 = New Section();
$s1->setLabel('dashboard');
$s1->save();

$r1 = New Right();
$r1->setSection($s1->getId());
$r1->setRead('1');
$r1->setDelete('1');
$r1->setCreate('1');
$r1->setUpdate('1');
$r1->setRank('1');
$r1->save();


$entity->customQuery('INSERT INTO "yana_plugin_dashboard" ("id", "model", "data", "cell", "column", "minified","dashboard") VALUES (1,	\'dash_profil\',	\'null\',	\'0\',	\'0\',	\'\',\''.$dashboard.'\');');
$entity->customQuery('INSERT INTO "yana_plugin_dashboard" ("id", "model", "data", "cell", "column", "minified","dashboard") VALUES (2,	\'dash_monitoring_ram\',	\'null\',	\'0\',	\'1\',	\'\',\''.$dashboard.'\');');
$entity->customQuery('INSERT INTO "yana_plugin_dashboard" ("id", "model", "data", "cell", "column", "minified","dashboard") VALUES (3,	\'dash_monitoring_system\',	\'null\',	\'0\',	\'2\',	\'\',\''.$dashboard.'\');');
$entity->customQuery('INSERT INTO "yana_plugin_dashboard" ("id", "model", "data", "cell", "column", "minified","dashboard") VALUES (4,	\'dash_monitoring_network\',	\'null\',	\'0\',	\'2\',	\'\',\''.$dashboard.'\');');
$entity->customQuery('INSERT INTO "yana_plugin_dashboard" ("id", "model", "data", "cell", "column", "minified","dashboard") VALUES (5,	\'dash_monitoring_hdd\',	\'null\',	\'3\',	\'1\',	\'\',\''.$dashboard.'\');');
$entity->customQuery('INSERT INTO "yana_plugin_dashboard" ("id", "model", "data", "cell", "column", "minified","dashboard") VALUES (6,	\'dash_monitoring_disk\',	\'null\',	\'0\',	\'1\',	\'\',\''.$dashboard.'\');');
$entity->customQuery('INSERT INTO "yana_plugin_dashboard" ("id", "model", "data", "cell", "column", "minified","dashboard") VALUES (7,	\'dash_monitoring_gpio\',	\'null\',	\'1\',	\'0\',	\'\',\''.$dashboard.'\');');
$entity->customQuery('INSERT INTO "yana_plugin_dashboard" ("id", "model", "data", "cell", "column", "minified","dashboard") VALUES (8,	\'dash_monitoring_services\',	\'null\',	\'0\',	\'2\',	\'\',\''.$dashboard.'\');');
$entity->customQuery('INSERT INTO "yana_plugin_dashboard" ("id", "model", "data", "cell", "column", "minified","dashboard") VALUES (9,	\'dash_monitoring_users\',	\'null\',	\'0\',	\'0\',	\'\',\''.$dashboard.'\');');


?>
