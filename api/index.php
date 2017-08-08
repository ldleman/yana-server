<?php
header('Content-Type: application/json; charset=utf-8');
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'common.php');


function api_default($_,&$response){
	global $myUser;
	if(!isset($_['object'])) throw new Exception('L\'objet doit être précisé');
	if(!$myUser->connected()) throw new Exception('L\'utilisateur doit être connecté');
	
	$response = array_merge($response,UserApi::route($_,'user'));
	$response = array_merge($response,SystemApi::route($_,'system'));
	$response = array_merge($response,GpioApi::route($_,'gpio'));
	$response = array_merge($response,IpApi::route($_,'ip'));
	
}

Plugin::addHook("api", "api_default");


$response = array();
try{
	if(!$myUser->connected()){

		if(isset($_['token'])){

			$userManager = new User();
			$myUser = $userManager->load(array('token'=>$_['token']));

			if(!isset($myUser) || !$myUser) throw new Exception('Mauvais token');
			
			$myUser->loadRights();
		}
		if(isset($_['login'])){
			$userManager = new User();
			$myUser = $userManager->load(array('login'=>$_['login'],'password'=>$_['password']));
			if(!$myUser) throw new Exception('Mauvais identifiant ou mot de passe');
			$myUser->loadRights();
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






abstract class Api {
	public static function route($_,$uri){
		$response = array();
		if($_['object']!= $uri) return $response;
		if(!method_exists(get_called_class(),$_['method'])) throw new Exception('Méthode :'.$_['method'].' non définie dans l\'objet '.$uri.'('.get_called_class().')');
		$class = get_called_class();
		return $class::$_['method']();
	}
}

class SystemApi extends Api{

	public static function get_cpu(){
		return Monitoring::cpu();
	}
	public static function get_heat(){
		return Monitoring::heat();
	}
	public static function get_disks(){
		return Monitoring::disks();
	}
	public static function get_ram(){
		return Monitoring::ram();
	}
		
}

class GpioApi extends Api{
	public static function get_gpio(){
		return  Monitoring::gpio();
	}
}

class IpApi extends Api{
	public static function get_wan(){
		return array(Monitoring::externalIp());
	}
	public static function get_lan(){
		return array(Monitoring::internalIp());
	}
}

class UserApi extends Api{
	public static function attributes(){
		global $conf,$myUser;
		$response['user'] = $myUser->toArray();
		unset($response['user']['password']);
		return $response;
	}

}

?>
