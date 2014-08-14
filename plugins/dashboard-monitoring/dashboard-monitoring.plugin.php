<?php
/*
@name dash_monitoring
@author Valentin CARRUESCO <valentin.carruesco@sys1.fr>
@link http://www.sys1.fr
@licence Copyright Sys1
@version 1.0.0
@description Ajoute des widgets de monitoring du raspberry pi
*/



function dash_monitoring_plugin_menu(&$widgets){
		$widgets[] = array(
		    'uid'      => 'dash_monitoring_ram',
		    'icon'     => 'fa fa-bar-chart-o',
		    'label'    => 'RAM',
		    'background' => '#50597B', 
		    'color' => '#fffffff',
		    'onLoad'   => 'action.php?action=dash_monitoring_plugin_load&bloc=ram',
		    'onMove'   => 'action.php?action=dash_monitoring_plugin_move&bloc=ram',
		    'onDelete' => 'action.php?action=dash_monitoring_plugin_delete&bloc=ram',
		);

		$widgets[] = array(
		    'uid'      => 'dash_monitoring_vocal',
		    'icon'     => 'fa fa-comments-o',
		    'label'    => 'Commandes vocales',
		    'background' => '#014B96', 
		    'color' => '#fffffff',
		    'onLoad'   => 'action.php?action=dash_monitoring_plugin_load&bloc=vocal',
		    'onMove'   => 'action.php?action=dash_monitoring_plugin_move&bloc=vocal',
		    'onDelete' => 'action.php?action=dash_monitoring_plugin_delete&bloc=vocal',
		);

		$widgets[] = array(
			'uid'      => 'dash_monitoring_system',
		    'icon'     => 'fa fa-tachometer',
		    'label'    => 'Système',
		    'background' => '#84C400', 
		    'color' => '#fffffff',
		    'onLoad'   => 'action.php?action=dash_monitoring_plugin_load&bloc=system',
		    'onMove'   => 'action.php?action=dash_monitoring_plugin_move&bloc=system',
		    'onDelete' => 'action.php?action=dash_monitoring_plugin_delete&bloc=system',
		);

		$widgets[] = array(
			'uid'      => 'dash_monitoring_network',
		    'icon'     => 'fa fa-exchange',
		    'label'    => 'Réseau',
		    'background' => '#006AFF', 
		    'color' => '#fffffff',
		    'onLoad'   => 'action.php?action=dash_monitoring_plugin_load&bloc=network',
		    'onMove'   => 'action.php?action=dash_monitoring_plugin_move&bloc=network',
		    'onDelete' => 'action.php?action=dash_monitoring_plugin_delete&bloc=network',
		);

		$widgets[] = array(
			'uid'      => 'dash_monitoring_hdd',
		    'icon'     => 'fa fa-hdd-o',
		    'label'    => 'HDD',
		    'background' => '#FF2E12', 
		    'color' => '#fffffff',
		    'onLoad'   => 'action.php?action=dash_monitoring_plugin_load&bloc=hdd',
		    'onMove'   => 'action.php?action=dash_monitoring_plugin_move&bloc=hdd',
		    'onDelete' => 'action.php?action=dash_monitoring_plugin_delete&bloc=hdd',
		);

		$widgets[] = array(
			'uid'      => 'dash_monitoring_disk',
		    'icon'     => 'fa fa-download',
		    'label'    => 'Disques',
		    'background' => '#C1004F', 
		    'color' => '#fffffff',
		    'onLoad'   => 'action.php?action=dash_monitoring_plugin_load&bloc=disk',
		    'onMove'   => 'action.php?action=dash_monitoring_plugin_move&bloc=disk',
		    'onDelete' => 'action.php?action=dash_monitoring_plugin_delete&bloc=disk',
		);

		$widgets[] = array(
			'uid'      => 'dash_monitoring_users',
		    'icon'     => 'fa fa-users',
		    'label'    => 'Utilisateurs',
		    'background' => '#E51400', 
		    'color' => '#fffffff',
		    'onLoad'   => 'action.php?action=dash_monitoring_plugin_load&bloc=users',
		    'onMove'   => 'action.php?action=dash_monitoring_plugin_move&bloc=users',
		    'onDelete' => 'action.php?action=dash_monitoring_plugin_delete&bloc=users',
		);

		$widgets[] = array(
			'uid'      => 'dash_monitoring_services',
		    'icon'     => 'fa fa-users',
		    'label'    => 'Services',
		    'background' => '#632F00', 
		    'color' => '#fffffff',
		    'onLoad'   => 'action.php?action=dash_monitoring_plugin_load&bloc=services',
		    'onMove'   => 'action.php?action=dash_monitoring_plugin_move&bloc=services',
		    'onDelete' => 'action.php?action=dash_monitoring_plugin_delete&bloc=services',
		);

		$widgets[] = array(
			'uid'      => 'dash_monitoring_gpio',
		    'icon'     => 'fa fa-dot-circle-o',
		    'label'    => 'GPIO',
		    'background' => '#373737', 
		    'color' => '#fffffff',
		    'onLoad'   => 'action.php?action=dash_monitoring_plugin_load&bloc=gpio',
		    'onMove'   => 'action.php?action=dash_monitoring_plugin_move&bloc=gpio',
		    'onDelete' => 'action.php?action=dash_monitoring_plugin_delete&bloc=gpio',
		);

		
}



function dash_monitoring_plugin_actions(){
	global $myUser,$_,$conf;

	switch($_['action']){

		case 'dash_monitoring_plugin_load':
			if($myUser==false) exit('Vous devez vous connecter pour cette action.');
			header('Content-type: application/json');

			$response = array();

			switch($_['bloc']){
				case 'ram':
					$response['title'] = 'RAM';

					$hdds = Monitoring::ram();
					$response['content'] = '
						<div style="width: 100%">
							<canvas id="RAM_PIE"></canvas>
							<br/><br/>
							<ul class="graphic_pane">
								<li class="pane_orange">
									<h1>RAM UTILISEE</h1>
									<h2>'.$hdds['percentage'].'%</h2>
								</li><li class="pane_cyan">
									<h1>RAM LIBRE</h1>
									<h2>'.$hdds['free'].' Mo</h2>
								</li><li class="pane_red">
									<h1>RAM TOTALE</h1>
									<h2>'.$hdds['total'].' Mo</h2>
								</li>
							</ul>
						</div>

						<script>

							$("#RAM_PIE:visible").chart({
								type : "doughnut",
								label : ["RAM UTILISEE","RAM LIBRE"],
								backgroundColor : ["'.($hdds['percentage']>80? '#E64C65' : '#FCB150' ).'","#4FC4F6"],
								segmentShowStroke:false,
								data : ['.$hdds['percentage'].','.(100-$hdds['percentage']).']
							});
							
						</script>';
				break;
				case 'system':
					$response['title'] = 'Système';
					
					if(PHP_OS!='WINNT'){
						$heat = Monitoring::heat();
						$cpu = Monitoring::cpu();
					}
			
					$response['content'] = '<ul>
				    	<li><strong>Distribution :</strong> '.Monitoring::distribution().'</li>
				    	<li><strong>Kernel :</strong> '.Monitoring::kernel().'</li>
				    	<li><strong>HostName :</strong> '.Monitoring::hostname().'</li>
				    	<li><strong>Température :</strong>  <span class="label '.$heat["label"].'">'.$heat["degrees"].'°C</span></li>
				    	<li><strong>Temps de marche :</strong> '.Monitoring::uptime().'</li>
				    	<li><strong>CPU :</strong>  <span class="label label-info">'.$cpu['current_frequency'].' Mhz</span> (Max '.$cpu['maximum_frequency'].'  Mhz/ Min '.$cpu['minimum_frequency'].'  Mhz)</li>
				    	<li><strong>Charge :</strong>  <span class="label label-info">'.$cpu['loads'].' </span>  | '.$cpu['loads5'].'  5min | '.$cpu['loads15'].'  15min</li>
				    </ul>';
				break;
				case 'vocal':

					if($myUser->getId()=='') exit('{"error":"invalid or missing token"}');
					if(!$myUser->can('vocal','r')) exit('{"error":"insufficient permissions for this account"}');
					
					list($host,$port) = explode(':',$_SERVER['HTTP_HOST']);
					$actionUrl = 'http://'.$host.':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
					$actionUrl = substr($actionUrl,0,strpos($actionUrl , '?'));
					
					Plugin::callHook("vocal_command", array(&$response,$actionUrl));

					
					$response['title'] = count($response['commands']).' Commandes vocales';
					$response['content'] = '<ul>';
					foreach($response['commands'] as $command){
						$response['content'] .= '<li title="Sensibilité : '.$command['confidence'].'">'.$command['command'].'</li>';
					}
					$response['content'] .= '</ul>';
				break;
				case 'network':
					$response['title'] = 'Réseau';
					$ethernet = array();
					$lan = '';
					$wan = '';
					$http = '';
					$connections = '';

					if(PHP_OS!='WINNT'){
						$ethernet = Monitoring::ethernet();
						$lan = Monitoring::internalIp();
						$wan = Monitoring::externalIp();
						$http = Monitoring::webServer();
						$connections = Monitoring::connections();
					}
			
					$response['content'] = '<ul>
					    	<li><strong>IP LAN :</strong> <code>'.$lan.'</code></li>
					    	<li><strong>IP WAN :</strong> <code>'.$wan.'</code></li>
					    	<li><strong>Serveur HTTP :</strong> '.$http.'</li>
					    	<li><strong>Ethernet :</strong> '.$ethernet['up'].' Montant / '.$ethernet['down'].' Descendant</li>
					    	<li><strong>Connexions :</strong>  <span class="label label-info">'.$connections.'</span></li>
					    </ul>';

				break;
				case 'gpio':
					$response['title'] = 'GPIO';
					$gpios = array();

					if(PHP_OS!='WINNT'){
						$gpios = Monitoring::gpio();
						$pin=array("GPIO 0","GPIO 1","GPIO 2","GPIO 3","GPIO 4","GPIO 5","GPIO 6","GPIO 7","   SDA","SCL   ","   CE0","CE1   ","  MOSI","MOSO  ","  SCLK","TxD   ","   RxD","GPIO 8","GPIO 9","GPIO10","GPIO11","JOKER!");
						$response['content'] .=  '<pre><ul>';
					    for ($i = 0; $i <= 21; $i+=2) {
					    	$class = 'info';
					    	$value = 'off';
					    	if($gpios[$i]){
					    		$class = 'warning';
					    		$value = 'on';
					    	}
					    	$class2 = 'info';
					    	$value2 = 'off';
					    	if($gpios[$i+1]){
					    		$class2 = 'warning';
					    		$value2 = 'on';
					    	}

					    	$response['content'] .=  '     <strong>'.$pin[$i].'</strong>-> <span onclick="change_gpio_state('.$i.',this);" style="width:20px;text-align:center;" class="label label-'.$class.' pointer">'.$value.'</span>  <span onclick="change_gpio_state('.$i.',this);" style="width:20px;text-align:center;" class="pointer label label-'.$class2.'">'.$value2.'</span>'.' <-<strong>'.$pin[($i+1)].'</strong><br/>';
					    }

				    	$response['content'] .=  '</ul></pre>';
					}else{
						$response['content'] .='Information indisponible sur cet OS :'.PHP_OS;
					}
			
				break;
				case 'users':
					$users = array();
					if(PHP_OS!='WINNT'){
						$users = Monitoring::users();
					}

					$response['title'] = count($users).' utilisateurs connectés';
					
					$response['content'] = '<ul>';
				    foreach ($users as $value) {
						$response['content'] .= '<li>Utilisateur <strong class="badge">'.$value['user'].'</strong> IP : <code>'.$value['ip'].'</code>, Connexion : '.$value['hour'].' </li>';
				    }
			    	$response['content'] .= '</ul>';
				break;
				case 'services':
					$response['title'] = 'Services';
					$services = array();
					
					if(PHP_OS!='WINNT'){
						$services = Monitoring::services();
						$response['content'] = '<ul>';
				    	foreach ($services as $value) {
				    		$response['content'] .= '<li '.($value['status']?'class="service-active"':'').'>- '.$value['name'].'</li>';
				    	}
				   		$response['content'] .= '</ul>';
					}else{
						$response['content'] .='Information indisponible sur cet OS :'.PHP_OS;
					}

				break;
				case 'hdd':
					$response['title'] = 'HDD';
					$hdds = array();
					
					if(PHP_OS!='WINNT'){
						$hdds = Monitoring::hdd();
						$response['content'] ='<ul>';
						foreach ($hdds as $value) {
							$response['content'] .='<li><strong class="badge">'.$value['name'].'</strong><br><strong> Espace :</strong> '.$value['used'].'/'.$value['total'].'<strong> Format :</strong> '.$value['format'].' </li>';
						}
						$response['content'] .='</ul>';
					}else{
						$response['content'] .='Information indisponible sur cet OS :'.PHP_OS;
					}

				break;

				case 'disk':

					$response['title'] = 'Disques';
					$disks = array();

					if(PHP_OS!='WINNT'){
						$disks = Monitoring::disks();
						$response['content'] ='<ul>';
					    foreach ($disks as $value) {
					    	$response['content'] .='<li><strong class="badge">'.$value['name'].'</strong> Statut : '.$value['size'].' Type : '.$value['type'].' Chemin : '.$value['mountpoint'].'  </li>';
					    }
					    $response['content'] .='</ul>';
					}else{
						$response['content'] .='Information indisponible sur cet OS :'.PHP_OS;
					}
		

				break;
			}

			echo json_encode($response);
			exit(0);

		break;

		case 'dash_monitoring_plugin_edit':
			echo '<label>Time Zone</label><input id="zone" type="text">';
		break;

		case 'dash_monitoring_plugin_save':

		break;
		case 'dash_monitoring_plugin_delete':

		break;
		case 'dash_monitoring_plugin_move':

		break;
	
	}
	
}

/*

<html>
<body>
<style>
.driver_content{
	padding:5px;
}
.driver_content .driver_image,.driver_content .driver_view{
	display:inline-block;
	vertical-align:top;
}
.driver_content .driver_image{
	width:35%;
}
.driver_content .driver_view{
	width:60%;
}
.driver_content .driver_properties{
	margin:10px 0 0 0;
	padding:0;
	list-style-type:none;
	width:100%;
	max-height:100px;
	overflow:auto;
}
.driver_content .driver_properties li{
	border-bottom:1px solid #222222;
	padding:3px;
}

.driver_content .driver_properties li .driver_property_label,.driver_content .driver_properties li .driver_property_value{
	display:inline-block;
	vertical-align:top;
}
.driver_content .driver_properties li .driver_property_label{
	width: 35%;
}
.driver_content .driver_properties li .driver_property_value{
	min-width: 60%;
}
</style>

<div class="driver_content">
	<div class="driver_image">
		<i class="fa fa-sun">image</i>
	</div>
	<div class="driver_view">
		19°
	</div>
	<label class="driver_properties_title">Propriétés</label>
	<ul class="driver_properties">
		<li>
			<div class="driver_property_label">Label</div><div class="driver_property_value">Valeur html</div>
		</li>
	</ul>
</div>


</body>
</html>
*/
Plugin::addCss('/css/style.css',true);
Plugin::addJs('/js/main.js',true);
Plugin::addHook("widgets", "dash_monitoring_plugin_menu");
Plugin::addHook("action_post_case", "dash_monitoring_plugin_actions");
?>