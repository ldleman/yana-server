<?php
/*
@name Temperature DS18B20
@author Arnaud LESUEUR <arnaud.lesueur@gmail.com>
@link https://github.com/alesueur
@licence CC by nc sa
@version 1.0.0
@description Permet de recuperer la temperature d'un capteur de type DS18B20
*/

function temperature_plugin_menu(&$menuItems){
	global $_;
	$menuItems[] = array('sort'=>10,'content'=>'<a href="index.php?module=temperature"><i class="icon-th-large"></i> Temperature</a>');
}

function temperature_plugin_page($_){
	if(isset($_['module']) && $_['module']=='temperature'){
	?>

	<h1>Capteur de Temperature</h1>
	<p>Retourne la temperature d'un capteur DS18B20</p>
	<h2>
<?php
    echo temperature_get();
	?>
	'C</h2>
	<p>Pour le montage a mettre en oeuvre : cela se passe <a href="http://learn.adafruit.com/adafruits-raspberry-pi-lesson-11-ds18b20-temperature-sensing/">ici<a>.</p>
	</div>
<?php
	}
}

function temperature_vocal_command(&$response,$actionUrl){
	global $conf;
	$response['commands'][] = array('command'=>$conf->get('VOCAL_ENTITY_NAME').' temperature','url'=>$actionUrl.'?action=temperature_action','confidence'=>'0.88');
}

function temperature_action(){
	global $_,$conf;

	switch($_['action']){
		case 'temperature_action':
			global $_;
				$affirmation = 'Il fait '.temperature_get().' degres';
				$response = array('responses'=>array(array('type'=>'talk','sentence'=>$affirmation)));
				$json = json_encode($response);
				echo ($json=='[]'?'{}':$json);
				
		break;
	}
}

function temperature_get(){
	if ($handle = opendir('/sys/bus/w1/devices')) {
		while (false !== ($entry = readdir($handle))) {
			if(!strncmp($entry, "28-" , strlen("28-"))) {
				$filename = "/sys/bus/w1/devices/".$entry."/w1_slave" ;
				if (file_exists($filename)) {
					$lines = file($filename);
					$currenttemp = round ( substr($lines[1], strpos($lines[1], "t=")+2) / 1000 , 1) ;
					closedir($handle);
					return $currenttemp;
				}
			}
		}
		closedir($handle);
	}
	return "N/A";
}

Plugin::addCss("/css/style.css"); 
//Plugin::addJs("/js/main.js"); 

Plugin::addHook("menubar_pre_home", "temperature_plugin_menu");  
Plugin::addHook("home", "temperature_plugin_page");
Plugin::addHook("vocal_command", "temperature_vocal_command");
Plugin::addHook("action_post_case", "temperature_action");

?>