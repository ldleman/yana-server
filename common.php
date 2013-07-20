<?php
session_start();
mb_internal_encoding('UTF-8');
error_reporting(E_ALL);
ini_set('display_errors','On');

$start=microtime(true);
require_once('constant.php');
if(!file_exists(DB_NAME)){ 
	header('location:install.php');
}else{
	if(file_exists('install.php')) $_GET['error'] = 'Par mesure de sécurité, pensez à supprimer le fichier install.php';
}


require_once('RainTPL.php');
function __autoload($class_name){
    include 'classes/'.$class_name . '.class.php';
}
//Calage de la date
date_default_timezone_set('Europe/Paris'); 
global $myUser,$conf,$_;
$myUser = (isset($_SESSION['currentUser'])?unserialize($_SESSION['currentUser']):false);
$userManager = new User();
$conf = new Configuration();
$conf->getAll();
//Instanciation du template
$tpl = new RainTPL();
//Definition des dossiers de template
raintpl::configure("base_url", null );
raintpl::configure("tpl_dir", './templates/'.DEFAULT_THEME.'/' );
raintpl::configure("cache_dir", "./cache/tmp/" );
$view = '';
$tpl->assign('myUser',$myUser);
$tpl->assign('userManager',$userManager);
$tpl->assign('configurationManager',$conf);
$rank = new Rank();
if($myUser!=false && $myUser->getRank()!=false){
	$rank = $rank->getById($myUser->getRank());
}
$tpl->assign('rank',$rank);
//Récuperation et sécurisation de toutes les variables POST et GET
$_ = array_map('Functions::secure',array_merge($_POST,$_GET));
$tpl->assign('_',$_);
$tpl->assign('action','');
$tpl->assign('error',(isset($_['error'])?urldecode($_['error']):false));
$tpl->assign('notice',(isset($_['notice'])?urldecode($_['notice']):false));
//Inclusion des plugins  
Plugin::includeAll();


?>