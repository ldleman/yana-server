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
	
	switch($_['object']):
		case 'user': 
			switch($_['method']):
				case 'attributes': 
					$response['user'] = $myUser->toArray();
					unset($response['user']['password']);
				break;
				
				default:
					throw new Exception('Méthode :'.$_['method'].' non définie');
				break;
			endswitch;
		break;
		
		default:
			throw new Exception('Objet :'.$_['object'].' non définit');
		break;
	endswitch;
}



Plugin::addHook("api", "api_plugin_api");


?>
