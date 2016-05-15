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


		case 'propise_save_sensor':
			
			Action::write(
				function($_,&$response){

					
					require_once('Sensor.class.php');
					$sensor = new Sensor();

					if(empty($_['labelSensor'])) throw new Exception("Le nom est obligatoire");
					if(empty($_['uidSensor']))  throw new Exception("L'UID est obligatoire");

					$sensor = !empty($_['id']) ? $sensor->getById($_['id']): new Sensor();
					$sensor->label = $_['labelSensor'];
					$sensor->location = $_['locationSensor'];
					$sensor->uid = $_['uidSensor'];
					$sensor->save();
					
					//Reference device for other plugins
					$device = new Device();
					$device->label = $sensor->label;
					$device->plugin = 'propise';
					$device->type = Device::CAPTOR;
					$device->location = $sensor->location;
					$device->icon = 'fa-heartbeat';
					$device->setValue('humidity',0);
					$device->setValue('temperature',0);
					$device->setValue('light',0);
					$device->setValue('mouvment',0);
					$device->setValue('sound',0);
					$device->state = 1;
					$device->uid = $sensor->id;
					$device->save();
					
					$response['message'] = 'Sonde enregistrée avec succès';
				},
				array('propise'=>'c')
			);
		break;

		case 'propise_delete_sensor':
			Action::write(
				function($_,$response){

					require_once('Sensor.class.php');
					$sensor = new Sensor();
					$sensor->delete(array('id'=>$_['id']));
				},
				array('propise'=>'d') 
			);
		break;


		case 'propise_add_data':
		
			/*for($i=0;$i<60;$i++){
				$_ = array(
				'uid'=>'sensor-2',
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
				if($sensor == null || $sensor->id==0) return;
				$data->time = time();
				//$data->time = strtotime(date('Ymd H:').$i.':00');
				$data->humidity = $_['humidity'];
				$data->temperature = $_['temperature'];
				$data->light = $_['light'];
				$data->mouvment = $_['mouvment'];
				$data->sound = $_['sound'];
				$data->sensor = $sensor->id;
				$data->save();
			//}
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
						$content .= '<li class="propise_temperature"><i class="fa fa-fire"></i> '.round($datas[0]->temperature,1).'°</li>';
						$content .= '<li class="propise_humidity"><i class="fa fa-tint"></i> '.round($datas[0]->humidity,1).'%</li>';
						$content .= '<li class="propise_light"><i class="fa fa-sun-o"></i> '.round($datas[0]->light,1).'%</li>';
						$content .= '<li class="propise_mouvment"><i class="fa fa-eye'.($datas[0]->mouvment==1?'':'-slash').'"></i></li>';
						$content .= '<li class="propise_sound"><i class="fa fa-bell'.($datas[0]->sound==1?'':'-slash').'"></i></li>';
						$content .= '</ul><ul class="propise_menu">';
						$content .= '<li class="propise_temperature" onclick="propise_menu(this)" data-view="temperature"><i class="fa fa-fire"></i></li>';
						$content .= '<li class="propise_humidity" onclick="propise_menu(this)" data-view="humidity"><i class="fa fa-tint"></i></li>';
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
			require_once(dirname(__FILE__).'/Sensor.class.php');
			$widget = new Widget();
			$widget = $widget->getById($_['id']);
			$data = $widget->data();
			$sensor = new Sensor();
			$sensors = $sensor->populate();
			$content = '<h3>Choix de la sonde</h3>';
			if(count($sensors) == 0):
			$content = 'Aucune sonde enregistrée,<a href="setting.php?section=propise">enregistrer une sonde<a>';
			else :
			$content .= '<select id="sensor">';
			foreach($sensors as $sensor)
				$content .= '<option '.($sensor->id==$data['sensor']?'selected="selected"':'').' value="'.$sensor->id.'">'.$sensor->label.' ('.$sensor->uid.')</option>';

			$content .= '</select>';
			endif;
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



function propise_plugin_setting_page(){
	global $_,$myUser,$conf;
	if(isset($_['section']) && $_['section']=='propise' ){
		require_once('Data.class.php');
			require_once('Sensor.class.php');

		if(!$myUser) throw new Exception('Vous devez être connecté pour effectuer cette action');
		$sensorManager = new Sensor();
		$sensors = $sensorManager->populate();
		$roomManager = new Room();
		$rooms = $roomManager->loadAll(array('state'=>'0'));
		$selected =  new Sensor();

		//Si on est en mode modification
		if (isset($_['id']))
			$selected = $sensorManager->getById($_['id']);
			

		
		?>

		<div class="span9 userBloc">

			<h1>Propise</h1>
			<p>Gestion des multi-sondes</p>  

			<fieldset>
			    <legend>Ajouter/Modifier une sonde</legend>

			    <div class="left">

				    <label for="labelSensor">Nom</label>
				    <input type="hidden" id="id" value="<?php echo $selected->id; ?>">
				    <input type="text" id="labelSensor" value="<?php echo $selected->label; ?>" placeholder="Sonde du salon"/>
			
				    <label for="uidSensor">UID</label>
				    <input type="text" value="<?php echo $selected->uid; ?>" id="uidSensor" placeholder="sonde-1,sonde-2..." />
				    

				    <label for="locationSensor">Pièce de la maison</label>
				    <select id="locationSensor">
				    	<?php foreach($rooms as $room){ ?>
				    	<option <?php if ($selected->location == $room->getId()){echo "selected";} ?> value="<?php echo $room->getId(); ?>"><?php echo $room->getName(); ?></option>
				    	<?php } ?>
				    </select>
				   
				</div>

	  			<div class="clear"></div>
			    <br/><button onclick="plugin_propise_save(this)" class="btn">Enregistrer</button>
		  	</fieldset>
			<br/>


			<fieldset>
				<legend>Consulter les sondes existants</legend>
				<table class="table table-striped table-bordered table-hover">
				    <thead>
					    <tr>
					    	<th>Nom</th>
						    <th>UID</th>
						    <th>Pièce</th>
						    <th colspan="2"></th>
						    
					    </tr>
				    </thead>
			    
			    	<?php foreach($sensors as $sensor){ 
			    		$room = $roomManager->load(array('id'=>$sensor->location)); 
			    	?>
					<tr>
				    	<td><?php echo $sensor->label; ?></td>
					    <td><?php echo $sensor->uid; ?></td>
					    <td><?php echo $room->getName(); ?></td>
					    <td>
					    	<a class="btn" href="setting.php?section=propise&id=<?php echo $sensor->id; ?>"><i class="fa fa-pencil"></i></a>
					    	<div class="btn" onclick="plugin_propise_delete(<?php echo $sensor->id; ?>,this);"><i class="fa fa-times"></i></div>
					    </td>
					    </td>
			    	</tr>
			    <?php } ?>
			    </table>
			</fieldset>
		</div>

<?php
	}
}

function propise_plugin_setting_menu(){
	global $_;
	echo '<li '.(isset($_['section']) && $_['section']=='propise'?'class="active"':'').'><a href="setting.php?section=propise"><i class="fa fa-angle-right"></i> Propise (Sondes)</a></li>';
}


function propise_plugin_page($_){
	if(!isset($_['module']) || $_['module']!='propise') return;
	require_once('Sensor.class.php');
	require_once('Data.class.php');
	$sensor = new Sensor();
	$data = new Data();
	$sensor = $sensor->getById($_['sensor']);
	


	$datas = $data->customQuery('SELECT * FROM '.MYSQL_PREFIX.'plugin_propise_data WHERE sensor='.$sensor->id.' ORDER BY `time` DESC LIMIT 30',true);

	$tab = array('hours'=>array(),'temperature'=>array(),'humidity'=>array(),'light'=>array());
	

	while($row = $datas->fetchArray() ){
		if(date('i',$row['time']) == "") continue;

		array_unshift($tab['hours'] , date('i',$row['time']));
		array_unshift($tab['temperature'] ,$row['temperature']);
		array_unshift($tab['humidity'] ,$row['humidity']);
		array_unshift($tab['light'] ,$row['light']);
	}




	?>
	<h3>Température</h3>
	<canvas style="width:100%;" id="chart_temperature" data-label="Temperature" data-hours="<?php echo '['.implode(',',$tab['hours']).']'; ?>"  data-data="<?php echo '['.implode(',',$tab['temperature']).']'; ?>"></canvas>
	<h3>Humidité</h3>
	<canvas style="width:100%;"  id="chart_humidity" data-label="Humidité" data-hours="<?php echo '['.implode(',',$tab['hours']).']'; ?>"  data-data="<?php echo '['.implode(',',$tab['humidity']).']'; ?>" ></canvas>
	<h3>Lumière</h3>
	<canvas style="width:100%;" id="chart_light" data-label="Lumière" data-hours="<?php echo '['.implode(',',$tab['hours']).']'; ?>" data-data="<?php echo '['.implode(',',$tab['light']).']'; ?>"></canvas>

	<?php
}


function propise_plugin_widget(&$widgets){
		$widgets[] = array(
		    'uid'      => 'dash_propise',
		    'icon'     => 'fa fa-tint',
		    'label'    => 'Propise',
		    'background' => '#6D6D6D', 
		    'color' => '#fffffff',
		    'onLoad'   => 'action.php?action=propise_load_widget',
		    'onEdit'   => 'action.php?action=propise_edit_widget',
		    'onSave'   => 'action.php?action=propise_save_widget',
		);
}



Plugin::addJs("/js/main.js");
//Lie wireRelay_plugin_setting_page a la zone réglages
Plugin::addHook("setting_bloc", "propise_plugin_setting_page");
//Lie wireRelay_plugin_setting_menu au menu de réglages
Plugin::addHook("setting_menu", "propise_plugin_setting_menu"); 
Plugin::addHook("action_post_case", "propise_action");    
Plugin::addHook("vocal_command", "propise_vocal_command");
Plugin::addHook("widgets", "propise_plugin_widget");
Plugin::addHook("home", "propise_plugin_page");  
?>
