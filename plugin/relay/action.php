<?php
	global $_,$conf;
	switch($_['action']){

		case 'relay_manual_change_state':
			Action::write(function($_,&$response){
				global $myUser;
				if(!$myUser->can('relay','update')) throw new Exception("Permissions insuffisantes");
				require_once('Relay.class.php');
				$relay = Relay::getById($_['id']);
				$type = Relay::types($relay->type);
				require_once($type['handler']);
				$class = str_replace('.class.php','',basename($type['handler']));
				
				$class::stateChange($relay,$_['state'],$response);	
			});
		break;

		case 'relay_search':
			Action::write(function($_,&$response){
				global $myUser;
				if(!$myUser->can('relay','read')) throw new Exception("Permissions insuffisantes");
				require_once('Relay.class.php');
				foreach(Relay::loadAll()as $relay){
					$relay->location = Room::getById($relay->location);
			    	$relay->type = Relay::types($relay->type);
			    	$metaArray = array();
			    	if(!empty($relay->meta)){
				    	foreach(json_decode($relay->meta) as $key=>$value){
				    		$metaArray[] = array('key'=>$key,'value'=>$value);
				    	}
			    	}
			    	$relay->meta = $metaArray;
					$response['rows'][] = $relay;
				}
					
			});
		break;
		
		case 'relay_save':
			Action::write(function($_,&$response){
				global $myUser;
				if(!$myUser->can('relay','edit')) throw new Exception("Permissions insuffisantes");
				require_once('Relay.class.php');
				$relay = !empty($_['id']) ? Relay::getById($_['id']) : new Relay();
				if(!isset($_['label']) || empty($_['label'])) throw new Exception("Nom obligatoire");
				$relay->label = $_['label'];
				$relay->description = $_['description'];
				$relay->icon = $_['icon'];
				$relay->type = $_['type'];
				$relay->location = $_['location'];
				$relay->meta = json_encode($_['meta']);

				$relay->save();

				//Enregistrement en tant que device yana
				$device = new Device();
				$device->label = $relay->label;
				$device->plugin = 'relay';
				$device->type = Device::ACTUATOR;
				$device->location = $relay->location;
				$device->icon = 'fa-hand-o-up';
				$device->setValue('state',0);
				$device->state = 1;
				$device->uid = $relay->id;
				$device->save();

			});
		break;
		
		case 'relay_edit':
			Action::write(function($_,&$response){
				global $myUser;
				if(!$myUser->can('relay','edit')) throw new Exception("Permissions insuffisantes");
				require_once('Relay.class.php');
				$relay = Relay::getById($_['id']);
				$relay->meta = json_decode($relay->meta);
				$response = $relay;
			});
		break;

		case 'relay_change_type':
			Action::write(function($_,&$response){
				global $myUser;
				require_once('Relay.class.php');
				if(!$myUser->can('relay','edit')) throw new Exception("Permissions insuffisantes");
				if(!isset($_['type']))  throw new Exception("Type non spécifié");
				$type = Relay::types($_['type']);
				if(empty($type))  throw new Exception("Type introuvable");
				if(!file_exists($type['handler'])) throw new Exception("Fichier handler : '".$type['handler']."'' introuvable");
				require_once($type['handler']);
				$class = str_replace('.class.php','',basename($type['handler']));
				ob_start();
				$class::settings();
				$response['html'] = ob_get_clean();
			});
		break;

		case 'relay_delete':
			Action::write(function($_,&$response){
				global $myUser;
				if(!$myUser->can('relay','delete')) throw new Exception("Permissions insuffisantes");
				require_once('Relay.class.php');
				Relay::deleteById($_['id']);
			});
		break;

		case 'relay_widget_configure':
			Action::write(function($_,&$response){
				require_once(__DIR__.SLASH.'Relay.class.php');
				$response['content'] = '<h4><i class="fa fa-wrench"></i> Configuration relais</h4>'; 
				$response['content'] .= 'Sélectionnez le relais à lier à ce widget :';
				$response['content'] .= '<select id="relay">';
				foreach(Relay::loadAll() as $relay){
					$response['content'] .= '<option value="'.$relay->id.'">'.$relay->label.'</option>';
				}
				$response['content'] .= '</select>';
			});
		break;


		case 'relay_widget_load':
			$widget = Widget::current();
			$id = $widget->data('relay');
			
			if($id==''){
				$content = '
				<div class="relay-no-configuration">
				<h4><i class="fa fa-adjust"></i> Aucun relais configuré</h4>
				<p>Cliquez sur la clé à molette pour configurer ce widget</p>
				</div>';
				
			}else{
				require_once('Relay.class.php');
				$relay =  Relay::getById($id);
				$iconColors = Relay::availableIcon($relay->icon);

				$type = Relay::types($relay->type);
				require_once($type['handler']);
				$class = str_replace('.class.php','',basename($type['handler']));
				ob_start();
				$class::widget($relay);
				$typeHtml = ob_get_clean();

				$content = '<div class="relay_widget" data-id="'.$relay->id.'">';
				

				$content .= '<li class="relay_case './*(Gpio::read($relay->pin)?'active':'').*/'" data-id="'.$relay->id.'" onclick="relay_change_state(this);" style="text-align:center;">
									<i title="On/Off" class="fa '.$relay->icon.'"></i>
								</li>
								<li>
									'.$typeHtml.'
									
								</li>';
				$content .= '</div>';

				$content .= '<style>
				.relay_widget.active .relay_case i.'.$relay->icon.'{
					color:'.$iconColors[0].';
					text-shadow: 0 0 10px '.$iconColors[1].';
				}
				</style>';

				$widget->title =  $relay->label;
			}

			$widget->content =  $content;
			
			echo json_encode($widget);
		break;

	}


?>