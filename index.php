<?php
require_once(dirname(__FILE__).'/header.php');

Plugin::callHook("index_pre_treatment", array(&$_));


if(!$myUser){
	$view = 'login';
}else{
	$view = 'index';
	if($conf->get('HOME_PAGE') != '' && $conf->get('HOME_PAGE')!='index.php')
	header('location: '.$conf->get('HOME_PAGE'));
}

require_once(dirname(__FILE__).'/footer.php');
?>
