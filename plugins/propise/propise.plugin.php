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
		'command'=>$conf->get('VOCAL_ENTITY_NAME').' diagnostique de la pièce',
		'callback'=>'propise_diagnostic',
		'confidence'=>0.8);
	
}

function propise_diagnostic($text,$confidence,$parameters,$myUser){
	global $conf;
	require_once('Sensor.class.php');
	require_once('Data.class.php');
	$sensor  = new Sensor();
	$data  = new Data();
	$sensor = $sensor->load(array('location'=>$text));
	$data  = $data->load(array('sensor'=>$sensor->id));
	$cli = new Client();
	$cli->connect();
	$cli->talk("Diagnostique pièce : ".$text);
	$cli->talk("Humidité : ".$data->humidity
	.", température : ".$data->temperature
	.", Luminosité : ".$data->temperature
	."%, mouvement : ".$data->mouvment
	."%, bruit : ".$data->sound
	);
	$cli->disconnect();
}




function propise_action(){
	global $_,$conf;
	switch($_['action']){
		case 'propise_add_data':
		
			/*for($i=0;$i<60;$i++){
				$_ = array(
				'uid'=>'sensor-1',
				'humidity'=>rand(0,100),
				'temperature'=>rand(0,100),
				'light'=>rand(0,100),
				'mouvment'=>rand(0,1),
				'sound'=>rand(0,1)
				);*/
				require_once('Sensor.class.php');
				require_once('Data.class.php');
				$sensor  = new Sensor();
				$data  = new Data();
				
				$sensor = $sensor->load(array('uid'=>$_['uid']));
				//$data->time = strtotime(date('Ymd H:').$i.':00');
				$data->time = time();
				$data->humidity = $_['humidity'];
				$data->temperature = $_['temperature'];
				$data->light = $_['light'];
				$data->mouvment = $_['mouvment'];
				$data->sound = $_['sound'];
				$data->sensor = $sensor->id;
				$data->save();
		//	}
		break;
		
		case 'propise_load_widget':

			require_once(dirname(__FILE__).'/../dashboard/Widget.class.php');
			
			Action::write(
				function($_,&$response){	

					$widget = new Widget();
					$widget = $widget->getById($_['id']);
					$parameters = $widget->data();

					if(empty($parameters['sensor'])){
						$content = 'Choisissez une localisation en cliquant sur l \'icone <i class="fa fa-wrench"></i> de la barre du widget';
					}else{
						
						
						global $conf;
						require_once('Data.class.php');
						require_once('Sensor.class.php');
						$sensor  = new Sensor();
						$data  = new Data();
						$sensor = $sensor->getById($parameters['sensor']);
						$datas = $data->loadAll(array('sensor'=>$sensor->id),'time DESC',1);
						
						

						$response['title'] = 'Sonde '.$sensor->label.' ('.$sensor->uid.')';


						$content = '
						<!-- CSS -->
						<style>
							.propise_view{
								background:#36B3E1;
								color:#ffffff;
								margin:0px;
								padding:10px;
								width:100%;
								list-style-type:none;
								transition:all 0.2s ease-in-out;
								box-sizing: border-box;
							}
							.propise_menu{
								margin:0;
							}
							.propise_menu li{
								cursor: pointer;
								display: inline-block;
								font-size: 20px;
								height: 25px;
								margin-top: 3px;
								padding: 5px;
								text-align: center;
								transition: all 0.2s ease-in-out 0s;
								width: 30px;
								border-top:4px solid #cecece;
							}
							
							color: #cecece;
    
							
							.propise_menu li:hover{
								background:#222222;
								color:#ffffff;
							}
							
							.propise_view li{
								display:none;
								font-family: "Open Sans Light",sans-serif;
								text-align:center;
								padding:15px 0;
								font-weight:300;
								font-size:60px;
							}
							
							.propise_view li.propise_temperature{
								display:block;
							}
							
							.propise_menu li.propise_temperature{
								border-color:#36B3E1;
							}
							.propise_menu li.propise_humidity{
								border-color:#50597B;
							}
							.propise_menu li.propise_light{
								border-color:#EF4D66;
							}
							.propise_menu li.propise_mouvment{
								border-color:#FFBF4C;
							}
							.propise_menu li.propise_sound{
								border-color:#84C400;
							}
							.propise_menu li.propise_stats{
								border-color:#C1004F;
							}
						</style>
						
						<!-- HTML -->
						';
						
						$content .= '<div class="propise_widget"><ul class="propise_view">';
						$content .= '<li class="propise_temperature"><i class="fa fa-fire"></i>'.$datas[0]->temperature.'°</li>';
						$content .= '<li class="propise_humidity"><i class="fa fa-cloud"></i>'.$datas[0]->humidity.'%</li>';
						$content .= '<li class="propise_light"><i class="fa fa-sun-o"></i>'.$datas[0]->light.'%</li>';
						$content .= '<li class="propise_mouvment"><i class="fa fa-eye'.($datas[0]->mouvment?'':'-slash').'"></i></li>';
						$content .= '<li class="propise_sound"><i class="fa fa-bell'.($datas[0]->sound?'':'-slash').'-o"></i></li>';
						$content .= '</ul><ul class="propise_menu">';
						$content .= '<li class="propise_temperature" onclick="propise_menu(this)" data-view="temperature"><i class="fa fa-fire"></i></li>';
						$content .= '<li class="propise_humidity" onclick="propise_menu(this)" data-view="humidity"><i class="fa fa-cloud"></i></li>';
						$content .= '<li class="propise_light" onclick="propise_menu(this)" data-view="light"><i class="fa fa-sun-o"></i></li>';
						$content .= '<li class="propise_mouvment" onclick="propise_menu(this)" data-view="mouvment"><i class="fa fa-eye"></i></li>';
						$content .= '<li class="propise_sound" onclick="propise_menu(this)" data-view="sound"><i class="fa fa-bell"></i></li>';
						$content .= '<li class="propise_stats" onclick="window.open(\'index.php?module=propise\')"><i class="fa fa-line-chart"></i></li>';
						$content .= '</ul>';
						$content .= '</div>';
						$content .= '
						<!-- JS -->
						<script type="text/javascript">
							function propise_menu(element){
								var container = $(element).closest(".propise_widget");
								$(".propise_view li",container).hide();
								$(".propise_view").css("background-color",$(element).css("border-top-color"));
								$(".propise_view li.propise_"+$(element).data("view"),container).fadeIn();
							};
							
						</script>
						';
					}
					$response['content'] = $content;
				}
			);
		break;

		case 'propise_edit_widget':
			require_once(dirname(__FILE__).'/../dashboard/Widget.class.php');
			require_once(dirname(__FILE__).'Sensor.class.php');
			$widget = new Widget();
			$widget = $widget->getById($_['id']);
			$data = $widget->data();
			$sensor = new Sensor();
			$sensors = $sensor->populate();
			$content = '<h3>Localisation sonde</h3>';
			$content .= '<select id="sensor">';
			foreach($sensors as $sensor)
				$content .= '<option '.($sensor->id==$data['sensor']?'selected="selected"':'').' value="'.$sensor->id.'">'.$sensor->label.' ('.$sensor->uid.')</option>';

			$content .= '</select>';
			echo $content;
		break;

		case 'propise_save_widget':
			require_once(dirname(__FILE__).'/../dashboard/Widget.class.php');
			$widget = new Widget();
			$widget = $widget->getById($_['id']);
			$data = $widget->data();
			
			$data['sensor'] = $_['sensor'];
			$widget->data($data);
			$widget->save();
			echo $content;
		break;
	}

}

function propise_plugin_widget(&$widgets){
		$widgets[] = array(
		    'uid'      => 'dash_propise',
		    'icon'     => 'fa fa-feed',
		    'label'    => 'Propise',
		    'background' => '#50597b', 
		    'color' => '#fffffff',
		    'onLoad'   => 'action.php?action=propise_load_widget',
		    'onEdit'   => 'action.php?action=propise_edit_widget',
		    'onSave'   => 'action.php?action=propise_save_widget',
		);
}



Plugin::addHook("action_post_case", "propise_action");    
Plugin::addHook("vocal_command", "propise_vocal_command");
Plugin::addHook("widgets", "propise_plugin_widget");
?>
