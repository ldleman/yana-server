<?php
require_once('header.php');

Plugin::callHook("index_pre_treatment", array(&$_));

$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$tpl->assign('url_link',$protocol.$_SERVER['SERVER_ADDR'].'/'.basename(dirname(__FILE__)).'/action.php');
$view = !$myUser?'login':'index';

require_once('footer.php');
?>
