<?php
/*
@name Sonde ( modèle Poolp )
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@description Permet la récuperations d'informations de temperatures, humidités, lumière, mouvement et sons dans une pièce a travers la sonde yana "poolp"
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
	
	$data  = new Data();
	$sensor = Sensor::load(array('location'=>$text));
	$data  = Data::load(array('sensor'=>$sensor->id));
	$cli = new Client();
	$cli->connect();
	$cli->talk("Diagnostique pièce : ".$text);
	$cli->talk("Humidité : ".$data->humidity
	.", température : ".$data->temperature
	.", Luminosité : ".$data->temperature
	."%, mouvement : ".$data->mouvment
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
					

					if(empty($_['labelSensor'])) throw new Exception("Le nom est obligatoire");

					$sensor = !empty($_['id']) ? Sensor::getById($_['id']): new Sensor();
					$sensor->label = $_['labelSensor'];
					$sensor->location = $_['locationSensor'];
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
					Data::delete(array('sensor'=>$_['id']));
					Sensor::deleteById($_['id']);
				},
				array('propise'=>'d') 
			);
		break;

		case 'propise_get_data':
			Action::write(
				function($_,&$response){
				require_once('Sensor.class.php');
				require_once('Data.class.php');
				$datas = Data::loadAll(array('sensor'=>$_['id']),'time DESC');
				$response['light'] = propise_convert_light($datas[0]->light);
				$response['humidity'] = $datas[0]->humidity;
				$response['temperature'] = $datas[0]->temperature;
				$response['mouvment'] = $datas[0]->mouvment;
			},
			array('propise'=>'r') );
		break;

		case 'propise_add_data':
			require_once('Sensor.class.php');
			require_once('Data.class.php');
			
			$sensor = Sensor::getById($_['id']);
			if($sensor == null || $sensor->id==0) return;
			$sensor->ip = $_['ip'];
			$sensor->save();
			
			$data  = new Data();
			$data->time = time();
			$data->humidity = $_['humidity'];
			$data->temperature = $_['temperature'];
			$data->light = $_['light'];
			$data->mouvment = $_['mouvment'];
			$data->sensor = $sensor->id;
			$data->save();
			
		
			echo '1';
		break;
		
		case 'propise_select_widget_menu':
		require_once(__DIR__.'/../dashboard/Widget.class.php');
			Action::write(
				function($_,&$response){
				$widget = new Widget();
				$widget = $widget->getById($_['id']);
				$data = $widget->data();
				
				$data['menu'] = $_['menu'];
				$widget->data($data);
				$widget->save();
			});
		break;
		
		case 'propise_load_widget':

			require_once(__DIR__.'/../dashboard/Widget.class.php');
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
						
					
						$sensor = Sensor::getById($parameters['sensor']);
						$datas = Data::loadAll(array('sensor'=>$sensor->id),'time DESC',1);
	
						$response['title'] = $sensor->label;

						$content = '
						<!-- CSS -->
						<style>
							
							.propise_menu{
								margin:0;
							}
							.propise_menu li{
								cursor: pointer;
								display: inline-block;
								font-size: 20px;
								height: 25px;
								margin-top: 3px;
								padding: 5px 5px 0 5px;
								text-align: center;
								color:#222222;
								transition: all 0.2s ease-in-out 0s;
								width: 30px;
								border-top:4px solid #222222;
							}

							.propise_menu li:hover{
								background:#222222;
								color:#ffffff;
							}
							
							.propise_view{
								background:#222222;
								color:#ffffff;
								margin:0px;
								padding:0px;
								width:100%;
								list-style-type:none;
								transition:all 0.2s ease-in-out;
								box-sizing: border-box;
							}
							.propise_view ul{
								padding:0;
								margin:0;
							}
							.propise_view li{
								font-family: "Open Sans Light",sans-serif;
								text-align:center;
								padding:30px 15px 15px 15px;
								list-style-type:none;
								float:left;
								height: 100px;
								width:50%;
								box-sizing:border-box;
								font-weight:300;
								font-size:40px;
								
							}
							
							.propise_view.propise_detail_view li{
								font-size:60px;
								width:100%;
								height:140px;
								font-size:50px;
								padding:40px 0 40px 0;
								float:none;
							}
							
							.propise_menu li[data-view="temperature"]{
								border-color:#36B3E1;
							}
							.propise_menu li[data-view="humidity"]{
								border-color:#50597B;
							}
							.propise_menu li[data-view="light"]{
								border-color:#EF4D66;
							}
							.propise_menu li[data-view="mouvment"]{
								border-color:#FFBF4C;
							}
							.propise_menu li[data-view="stats"]{
								border-color:#b3e021;
							}
							
							
							.propise_view li[data-type="temperature"]{
								background-color:#36B3E1;
								border-color:#36B3E1;
							}
							.propise_view li[data-type="humidity"]{
								background-color:#50597B;
								border-color:#50597B;
							}
							.propise_view li[data-type="light"]{
								background-color:#EF4D66;
								border-color:#EF4D66;
							}
							.propise_view li[data-type="mouvment"]{
								background-color:#FFBF4C;
								border-color:#FFBF4C;
							}
							.propise_view li[data-type="propise_stats"]{
								background-color:#C1004F;
								border-color:#C1004F;
							}
						</style>
						
						<!-- HTML -->
						';
						
						$content .= '<div class="propise_widget" data-view="'.$parameters['menu'].'" data-id="'.$sensor->id.'">
						<div class="propise_view">
							
								<ul>
									<li data-type="light"><i class="fa fa-sun-o fa-spin-low"></i> <span ></span>%</li>
									<li data-type="temperature"><i class="fa fa-fire"></i> <span ></span>°</li>
									<li data-type="humidity"><i class="fa fa-tint"></i> <span ></span>%</li>
									<li data-type="mouvment"><i class="fa fa-eye"></i> <span ></span></li>
								</ul>
								<div class="clear"></div>
							
						';
				
						$content .= '</div>';
						
						$content .= '<ul class="propise_menu">';
							$content .= '<li class="propise_global" onclick="propise_menu(this)" data-view=""><i class="fa fa-columns"></i></li>';
							$content .= '<li onclick="propise_menu(this)" data-view="light"><i class="fa fa-sun-o"></i></li>';
							$content .= '<li onclick="propise_menu(this)" data-view="temperature"><i class="fa fa-fire"></i></li>';
							$content .= '<li onclick="propise_menu(this)" data-view="humidity"><i class="fa fa-tint"></i></li>';
							$content .= '<li onclick="propise_menu(this)" data-view="mouvment"><i class="fa fa-eye"></i></li>';
							$content .= '<li onclick="window.open(\'index.php?module=propise&section=stats&id='.$sensor->id.'\')" data-view="stats"><i class="fa fa-line-chart"></i></li>';
						$content .= '</ul>';
						
						$content .= '</div>';
						$content .= '
						<!-- JS -->
						<script type="text/javascript">
							$("document").ready(function(){
								propise_refresh_data();
								setInterval(propise_refresh_data,2000);
							});
							
							function propise_refresh_data(){
								$(".propise_widget[data-id='.$sensor->id.']").each(function(i,element){
										var view = $(element).attr("data-view");
										
										if(view != $(element).attr("data-selected")){
											propise_show($(element),view);
											console.log("ee");
										}

										

										$.action({
											action:"propise_get_data",
											id:$(element).attr("data-id")
										},function(r){
											for(var key in r){
												var type = $(\'li[data-type="\'+key+\'"]\');
												if( key.length ==0 ) continue;
												$("span",type).html(r[key]);
											}

											var mouvment = $("[data-type=\'mouvment\']");
											$("i",mouvment).attr("class","fa fa-eye"+($("span",mouvment).text()==1?"":"-slash"));
										
											
										});
									});
							}
							
							function propise_menu(element,global){
								var line = $(element);
								var container = line.closest(".propise_widget");
								var view = $(element).attr("data-view");
								var widget = $(element).closest(\'.dashboard_bloc\').attr(\'data-id\');
								
								$(container).attr("data-view",view);
								$.action({
									action:"propise_select_widget_menu",
									id:widget,
									menu: view
								});

								propise_show(container,view);
							};
							
							function propise_show(container,view){
								
								$(container).attr("data-selected",view);
								
								if(view==\'\'){
									$(".propise_view ul li",container).fadeIn();
									$(".propise_view",container).removeClass("propise_detail_view")
									return;
								}
								
								$(".propise_view",container).addClass("propise_detail_view")
								$(".propise_view ul li",container).hide();
								$(".propise_view").css("background-color",$(".propise_view ul li[data-view=\'"+view+"\']",container).css("border-top-color"));
								$(".propise_view ul li[data-type=\'"+view+"\']",container).fadeIn();
							}
							
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

function propise_convert_light($light){
	return round(($light*100)/1024,1);
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

			<h1>Sondes Poolp</h1>
			<p>Gestion des multi-sondes</p>  

			<fieldset>
			    <legend>Ajouter/Modifier une sonde</legend>

			    <div class="left">

				    <label for="labelSensor">Nom</label>
				    <input type="hidden" id="id" value="<?php echo $selected->id; ?>">
				    <input type="text" id="labelSensor" value="<?php echo $selected->label; ?>" placeholder="Sonde du salon"/>
			
				   <!--  <label for="uidSensor">UID</label>
				    <input type="text" value="<?php echo $selected->uid; ?>" id="uidSensor" placeholder="sonde-1,sonde-2..." />
				     -->

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
							<th>A copier dans la sonde</th>
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
					    	<a onclick="$(this).next().toggle()" class="btn">Guide Installation</a>
					    	<ul style="display:none;">
					    		<li>Démarrer la sonde en appuyant sur le boutton jusqu'a ce que la lumière s'allume en bleu</li>
					    		<li>Se connecter au wifi de la sonde (PROPISE-XX) avec le mot de passe bananeflambee</li>
					    		<li>Ouvrir l'interface de la sonde sur <a href="http://192.168.4.1">http://192.168.4.1</a></li>
					    		<li>Remplir les identifiant WIFI de votre réseau</li>
					    		<li>Dans le dernier champs, mettre le lien suivant :
					    <?php 
					    $url = YANA_URL.'/action.php?action=propise_add_data&id='.$sensor->id.'&light={{LIGHT}}&humidity={{HUMIDITY}}&temperature={{TEMPERATURE}}&mouvment={{MOUVMENT}}';
					    echo '<a href="'.$url.'">'.$url.'</a>'; ?>
							</li>
							</ul>
					   	</td>
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
	echo '<li '.(isset($_['section']) && $_['section']=='propise'?'class="active"':'').'><a href="setting.php?section=propise"><i class="fa fa-angle-right"></i> Sondes (Poolp)</a></li>';
}


function propise_plugin_page($_){
	if(!isset($_['module']) || $_['module']!='propise') return;
	require_once('Sensor.class.php');
	require_once('Data.class.php');
	
	$sensor = Sensor::getById($_['id']);
	$datas = Data::loadAll(array('sensor'=>$sensor->id),'time',30);
	$tab = array('hours'=>array(),'temperature'=>array(),'humidity'=>array(),'light'=>array());
	foreach($datas as $data){
		if(date('i',$data->time) == "") continue;
		array_unshift($tab['hours'] , date('i',$data->time));
		array_unshift($tab['temperature'] ,$data->temperature);
		array_unshift($tab['humidity'] ,$data->humidity);
		array_unshift($tab['light'] ,$data->light);
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
		    'label'    => 'Sonde Poolp',
		    'background' => '#ffffff', 
		    'color' => '#222222',
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