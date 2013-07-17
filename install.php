<?php
require_once('constant.php');
function __autoload($class_name) {
    include 'classes/'.$class_name . '.class.php';
}

$user = new User();
$configuration = new Configuration();
$right = new Right();
$rank = new Rank();
$section = new Section();

$configuration->create();
$user->create();
$right->create();
$rank->create();
$section->create();

$rank = new Rank();
$rank->setLabel('admin');
$rank->save();

$s1 = New Section();
$s1->setLabel('configuration');
$s1->save();	

$s2 = New Section();
$s2->setLabel('plugin');
$s2->save();	

$s3 = New Section();
$s3->setLabel('user');
$s3->save();	


$r1 = New Right();
$r1->setSection('1');
$r1->setRead('1');
$r1->setDelete('1');
$r1->setCreate('1');
$r1->setUpdate('1');
$r1->setRank('1');
$r1->save();

$r2 = New Right();
$r2->setSection('2');
$r2->setRead('1');
$r2->setDelete('1');
$r2->setCreate('1');
$r2->setUpdate('1');
$r2->setRank('1');
$r2->save();

$r3 = New Right();
$r3->setSection('3');
$r3->setRead('1');
$r3->setDelete('1');
$r3->setCreate('1');
$r3->setUpdate('1');
$r3->setRank('1');
$r3->save();
							

$user->setMail('admin@admin.com');
$user->setName('Admin');
$user->setFirstName('Admin');
$user->setPassword('admin');
$user->setLogin('admin');
$user->setToken(sha1(time().rand(0,1000)));
$user->setState(1);
$user->setRank(1);



$user->save();



Plugin::enabled('relay-relay');
Plugin::enabled('vocal_infos-vocal_infos');
Plugin::enabled('room-room');
?>