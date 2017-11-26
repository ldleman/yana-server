<?php 
session_name ('yana-server');
session_start();
mb_internal_encoding('UTF-8');

if(!file_exists(__DIR__.DIRECTORY_SEPARATOR.'constant.php'))
	header('location:install.php');


require_once(__DIR__.DIRECTORY_SEPARATOR.'constant.php');
require_once(__ROOT__.'/function.php');
date_default_timezone_set(TIME_ZONE);

set_error_handler('errorToException');
set_exception_handler ('unhandledException');
spl_autoload_register('app_autoloader');

global $myUser,$conf,$_,$success;
$_ = array_map('secure_user_vars', array_merge($_POST, $_GET));

$page = basename($_SERVER['PHP_SELF']);


$myUser = isset($_SESSION['currentUser']) ? unserialize($_SESSION['currentUser']) : new User();
$conf = new Configuration();
$conf->getAll();


	//MENUS
Plugin::addHook("menu_setting", function(&$settingMenu){
	global $myUser;

	if($myUser->can('dashboard','configure'))
		$settingMenu[]= array(
			'sort' =>2,
			'url' => 'setting.php?section=dashboard',
			'icon' => 'angle-right',
			'label' => 'Dashboard'
			);
	
	if($myUser->can('plugin','configure'))
		$settingMenu[]= array(
			'sort' =>0,
			'url' => 'setting.php?section=plugin',
			'icon' => 'angle-right',
			'label' => 'Plugins'
			);

	
	if($myUser->can('user','configure'))
		$settingMenu[]= array(
			'sort' =>1,
			'url' => 'setting.php?section=user',
			'icon' => 'angle-right',
			'label' => 'Utilisateurs'
			);
	
	if($myUser->can('room','configure'))
		$settingMenu[]= array(
			'sort' =>1,
			'url' => 'setting.php?section=room',
			'icon' => 'angle-right',
			'label' => 'Pièces'
			);

	if($myUser->can('rank','configure'))
		$settingMenu[]= array(
			'sort' =>2,
			'url' => 'setting.php?section=rank',
			'icon' => 'angle-right',
			'label' => 'Rangs & Accès'
			);
	if($myUser->can('log','read'))
		$settingMenu[]= array(
			'sort' =>2,
			'url' => 'setting.php?section=log',
			'icon' => 'angle-right',
			'label' => 'Logs'
			);
	
});

Plugin::addHook("menu_main", function(&$mainMenu){
	$mainMenu[]= array(
		'sort' =>0,
		'icon' => 'home',
		'label' => 'Accueil',
		'url' => 'index.php'
		);
	$settingMenu = array();
	Plugin::callHook("menu_setting", array(&$settingMenu));
	$mainMenu[]= array(
		'sort' =>100,
		'icon' => 'cog',
		'label' => 'Réglages',
		'url' => 'setting.php',
		'items' => $settingMenu
		);
});

Plugin::addHook("menu_user", function(&$userMenu){
	$userMenu[]= array(
		'sort' =>0,
		'label' => 'Modifier',
		'url' => 'account.php'
		);
});

Plugin::addHook("content_setting", function(){
	global $_;
	if(in_array($_['section'],array('plugin','rank','right','user','room','dashboard','log')) && file_exists('setting.'.$_['section'].'.php'))
		require_once('setting.'.$_['section'].'.php');
});


Plugin::addHook("vocal_command",function(&$commands){
	$commands['commands'][] = array(

		'command' => "quelle heure est il",
		'url' => "?action=get_hour",
		'confidence' => "0.88",
		'disabled' => false
	);

	});



Plugin::addHook('listen',function($command,$text,$confidence,$user){

		$cli = new Client();
		$cli->connect();
		$cli->talk('il est '.date('H:i'));
		$cli->disconnect();

	//$_['command'],trim(str_replace($_['command'],'',$_['text'])),$_['confidence'],$client->user
});

Plugin::addHook("section",function(&$sections){
	$sections['user'] = 'Gestion des utilisateurs';
	$sections['plugin'] = 'Gestion des plugins';
	$sections['rank'] = 'Gestion des rangs et droits';
	$sections['dashboard'] = 'Gestion de la dashboard et des widgets';
	$sections['room'] = 'Gestion des pièces';
	$sections['log'] = 'Gestion des logs programme';
});


Plugin::addHook("page",function(){
	global $_;
	if(!isset($_['module'])) 
		require_once(__ROOT__.'dashboard.php');
});

Plugin::includeAll();





?>