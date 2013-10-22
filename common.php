<?php
session_start();

ini_set('display_errors','1');

error_reporting(E_ALL & ~E_NOTICE);

//Idleman : Active les notice uniquement pour ma config reseau (pour le débug), pour les user il faut la désactiver
//car les notices peuvent gener les reponses json, pour les dev ajoutez votre config dans une même if en dessous.
if($_SERVER["HTTP_HOST"]=='192.168.0.14' && $_SERVER['REMOTE_ADDR']=='192.168.0.69') error_reporting(E_ALL); 

mb_internal_encoding('UTF-8');
$start=microtime(true);
global $myUser,$conf,$_;
//Récuperation et sécurisation de toutes les variables POST et GET
$_ = array_map('Functions::secure',array_merge($_POST,$_GET));
$error = '';
require_once('constant.php');


if(!file_exists(DB_NAME)){
	header('location:install.php');
}else{
	if(file_exists('install.php')) $error .= ($error!=''?'<br/>':'').'<strong>Attention: </strong> Par mesure de sécurité, pensez à supprimer le fichier install.php';
}


require_once('RainTPL.php');
$error = (isset($_['error']) && $_['error']!=''?'<strong>Erreur: </strong> '.str_replace('|','<br/><strong>Erreur: </strong> ',(urldecode($_['error']))):false);
$message = (isset($_['notice']) && $_['notice']!=''?'<strong>Message: </strong> '.str_replace('|','<br/><strong>Message: </strong> ',(urldecode($_['notice']))):false);

function __autoload($class_name){
    include 'classes/'.$class_name . '.class.php';
}
//Calage de la date
date_default_timezone_set('Europe/Paris'); 

$myUser = false;
$conf = new Configuration();
$conf->getAll();
//Inclusion des plugins  
Plugin::includeAll();

if(isset($_SESSION['currentUser'])){
	$myUser =unserialize($_SESSION['currentUser']);
}
if(!$myUser && isset($_COOKIE[COOKIE_NAME])){
	$users = User::getAllUsers();
	foreach ($users as $user) {
		if($user->getCookie() == $_COOKIE[COOKIE_NAME]) 
			{
				$myUser = $user;
				$myUser->loadRight();
			}
	}
}





$userManager = new User();

//Instanciation du template
$tpl = new RainTPL();
//Definition des dossiers de template
raintpl::configure("base_url", null );
raintpl::configure("tpl_dir", './templates/'.DEFAULT_THEME.'/' );
raintpl::configure("cache_dir", "./cache/tmp/" );
$view = '';

$rank = new Rank();
if($myUser!=false && $myUser->getRank()!=false){
	$rank = $rank->getById($myUser->getRank());
}


$tpl->assign('myUser',$myUser);
$tpl->assign('userManager',$userManager);
$tpl->assign('configurationManager',$conf);
$tpl->assign('error',$error);
$tpl->assign('notice',$message);
$tpl->assign('_',$_);
$tpl->assign('action','');
$tpl->assign('rank',$rank);
?>