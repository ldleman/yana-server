<?php
require_once('header.php');

Plugin::callHook("index_pre_treatment", array(&$_));
$view = (!$myUser?'login':'index');
require_once('footer.php');
?>
