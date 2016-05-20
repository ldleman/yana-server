<?php
/*
@name Debug
@author Valentin CARRUESCO <idleman@idleman.fr>
@link Http://blog.idleman.fr
@licence Cc -by-nc-sa
@version 1.1
@description Permet le debug pour tester les communications entre client(s) et serveur
*/


//cette fonction comprends toutes les actions du plugin qui ne nécessitent pas de vue html
function debug_plugin_action(){
	global $_,$conf,$myUser;

	//Action de réponse à la commande vocale "Yana, commande de test"
	switch($_['action']){

		case 'plugin_debug_send':
		require_once('Debug.class.php');
		try{
			
			$debugs = Debug::loadAll();
			$debug = $debugs[$_['debug']];
			$t = $debug->execute;
			$t();

			
		}catch(Exception $e){
			echo $e->getMessage();
		}

		break;

	}
}





function debug_plugin_setting_page(){
	global $_,$myUser,$conf;
	if(isset($_['section']) && $_['section']=='debug' ){
		require_once('Debug.class.php');
		try {
			
		
		if(!$myUser) throw new Exception('Vous devez être connecté pour effectuer cette action');

		?>
		<div class="form-inline">
		<select id="debug_selector">
			<?php foreach(Debug::loadAll() as $uid=>$debug): ?>
				<option value="<?php echo $uid; ?>"><?php echo  $debug->label; ?></option>
			<?php endforeach; ?>
		</select><button onclick="debug_plugin_send(this);">Envoyer</button>
		</div>
		<textarea id="debug_monitor" class="debug_monitor"></textarea>

		<?php

		} catch (Exception $e) {
			Functions::htmlAlert('error',$e->getMessage());
		}

	}
}

function debug_plugin_setting_menu(){
	global $_;
	echo '<li '.(isset($_['section']) && $_['section']=='debug'?'class="active"':'').'><a href="setting.php?section=debug"><i class="fa fa-angle-right"></i> Debug</a></li>';
}


/*
function debug_plugin_listen($command,$text,$confidence){
	//echo 'diction de la commande : '.$command;
}
*/



Plugin::addCss("/css/main.css"); 
Plugin::addJs("/js/main.js"); 


//Lie debug_plugin_setting_page a la zone réglages
Plugin::addHook("setting_bloc", "debug_plugin_setting_page");
//Lie debug_plugin_setting_menu au menu de réglages
Plugin::addHook("setting_menu", "debug_plugin_setting_menu"); 
//Lie debug_plugin_action a la page d'action qui perme d'effecuer des actionx ajax ou ne demdnant pas de retour visuels
Plugin::addHook("action_post_case", "debug_plugin_action");    
//Lie debug_plugin_vocal_command a la gestion de commandes vocales proposées par yana
//Plugin::addHook("vocal_command", "debug_plugin_vocal_command");


//Plugin::addHook("listen", "debug_plugin_listen");

?>