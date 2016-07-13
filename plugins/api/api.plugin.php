<?php
/*
@name Api
@author Valentin CARRUESCO <idleman@idleman.fr>
@link Http://blog.idleman.fr
@licence Cc -by-nc-sa
@version 1.0
@type component
@description API JSon pour l'interconnexion avec d'autres services
*/




function api_plugin_api(&$_,&$response){
	global $conf,$myUser;
	
	if(!isset($_['object'])) throw new Exception('L\'objet doit être précisé');
	if($myUser->getId()==0) throw new Exception('L\'utilisateur doit être connecté');
	
	$response = array_merge($response,UserApi::route($_,'user'));
	$response = array_merge($response,SystemApi::route($_,'system'));
	$response = array_merge($response,GpioApi::route($_,'gpio'));
	$response = array_merge($response,IpApi::route($_,'ip'));
}

Plugin::addHook("api", "api_plugin_api");

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
	public static function get_cpu(){
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
