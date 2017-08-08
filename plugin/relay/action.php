<?php
	global $_,$conf;
	switch($_['action']){

		case 'relay_manual_change_state':
			Action::write(function($_,&$response){
				global $myUser;
				if(!$myUser->can('relay','update')) throw new Exception("Permissions insuffisantes");
				require_once('WireRelay.class.php');
				$relay = WireRelay::getById($_['id']);
				//... TODO
					
			});
		break;

		case 'relay_search':
			Action::write(function($_,&$response){
				global $myUser;
				if(!$myUser->can('relay','read')) throw new Exception("Permissions insuffisantes");
				require_once('WireRelay.class.php');
				foreach(WireRelay::loadAll()as $relay){
					$relay->location = Room::getById($relay->location);
			    	$relay->type = Relay::types($relay->type);
					$response['rows'][] = $relay;
				}
					
			});
		break;
		
		case 'relay_save':
			Action::write(function($_,&$response){
				global $myUser;
				if(!$myUser->can('relay','edit')) throw new Exception("Permissions insuffisantes");
				require_once('WireRelay.class.php');
				$relay = !empty($_['id']) ? WireRelay::getById($_['id']) : new WireRelay();
				if(!isset($_['label']) || empty($_['label'])) throw new Exception("Nom obligatoire");
				$relay->label = $_['label'];
				$relay->description = $_['description'];
				$relay->icon = $_['icon'];
				$relay->type = $_['type'];
				$relay->location = $_['location'];
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
				require_once('WireRelay.class.php');
				$relay = WireRelay::getById($_['id']);
				$response = $relay;
			});
		break;

		case 'relay_delete':
			Action::write(function($_,&$response){
				global $myUser;
				if(!$myUser->can('relay','delete')) throw new Exception("Permissions insuffisantes");
				require_once('WireRelay.class.php');
				WireRelay::deleteById($_['id']);
			});
		break;

		case 'relay_widget_configure':
			Action::write(function($_,&$response){
				require_once(__DIR__.SLASH.'WireRelay.class.php');
				$response['content'] = '<h4><i class="fa fa-wrench"></i> Configuration relais</h4>'; 
				$response['content'] .= 'Sélectionnez le relais à lier à ce widget :';
				$response['content'] .= '<select id="relay">';
				foreach(WireRelay::loadAll() as $relay){
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
				require_once('WireRelay.class.php');
				$relay =  WireRelay::getById($id);
				$iconColors = Relay::availableIcon($relay->icon);

				$content = '<div class="relay_widget" data-id="'.$relay->id.'">';
				


				$content .= '<li class="relay_case './*(Gpio::read($relay->pin)?'active':'').*/'" data-id="'.$relay->id.'" onclick="relay_change_state(this);" style="text-align:center;">
									<i title="On/Off" class="fa '.$relay->icon.'"></i>
								</li>
								<li>
									<h2>'.$relay->description.'</h2>
									<h1>PIN './*$relay->pin.($relay->pulse!=0?' - Pulse '.$relay->pulse.'µs':'').*/'</h1>
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