<?php
require_once('header.php');

Plugin::callHook("index_pre_treatment", array(&$_));

$tpl->assign('users',Monitoring::users());
$tpl->assign('hdds',Monitoring::hdd());
$tpl->assign('services',Monitoring::services());
$tpl->assign('ethernet',Monitoring::ethernet());
$tpl->assign('ram',Monitoring::ram());
$tpl->assign('cpu',Monitoring::cpu());
$tpl->assign('heat',Monitoring::heat());
$tpl->assign('disks',Monitoring::disks());

$view = (!$myUser?'login':'index');

require_once('footer.php');
?>
