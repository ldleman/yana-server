<?php
header('Content-Type: application/json; charset=utf-8');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'common.php');

$response = array();
try{
	if(!$myUser){
		if(isset($_['token'])){
			$userManager = new User();
			$myUser = $userManager->load(array('token'=>$_['token']));
			if(isset($myUser) && $myUser!=false)
				$myUser->loadRight();
		}
		if(isset($_['login'])){
			$userManager = new User();
			$myUser = $userManager->load(array('login'=>$_['login'],'password'=>$_['password']));
			if(!$myUser) throw new Exception('Mauvais identifiant ou mot de passe');
			$myUser->loadRight();
		}
	}
	$myUser = (!$myUser?new User():$myUser);
	Plugin::callHook("api", array(&$_,&$response));
}catch(Exception $e){
	$response['error'] = Personality::response('WORRY_EMOTION').' : '.$e->getMessage();
}
$response = json_encode($response);
if(isset($_['callback']))
	$response = $_['callback'].'('.$response.');';

echo $response;
?>
