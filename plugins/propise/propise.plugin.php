<?php
/*
@name Propise : PROtotype de PIeuvre SEnsitive 
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@description Permet la récuperations d'informations de temperatures, humidités, lumière, mouvement et sons dans une pièce a travers une sonde ethernet maison (propise)
*/





function propise_vocal_command(&$response,$actionUrl){
	global $conf;


	$response['commands'][] = array(
		'command'=>$conf->get('VOCAL_ENTITY_NAME').' diagnostique de la piece',
		'callback'=>'propise_diagnostic',
		'confidence'=>0.8);
	
}

function propise_diagnostic($text,$confidence,$parameters,$myUser){
	global $conf;
	require_once('Data.class.php');
	$data  = new Data();
	$data = $data->load(array('location'=>$text));
	$cli = new Client();
	$cli->connect();
	$cli->talk("Diagostique pièce : ".$text);
	$cli->talk("Humidité : ".$data->humidity);
	$cli->disconnect();
}




function propise_action(){
	global $_,$conf;
	switch($_['action']){
		case 'propyse_add_data':
			require_once('Data.class.php');
			$data  = new Data();
			$data->time = time();
			$data->humidity = $_['humidity'];
			$data->location = $_['location'];
			$data->save();
		break;
		case 'propyse_get_data':
			global $conf;
			require_once('Data.class.php');
			$data  = new Data();
			$data = $data->load(array('location'=>$_['location']));
			var_dump($data);
			echo "Diagostique pièce : ".$_['location'];
			echo "Humidité : ".$data->humidity;
			
		break;
	}

}






Plugin::addHook("action_post_case", "propise_action");    
Plugin::addHook("vocal_command", "propise_vocal_command");
?>
