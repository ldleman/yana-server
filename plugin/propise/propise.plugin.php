<?php



function propise_plugin_install($id){
	if($id != 'fr.idleman.propise') return;
	Entity::install(__DIR__);
}
function propise_plugin_uninstall($id){
	if($id != 'fr.idleman.propise') return;
	Entity::uninstall(__DIR__);
}

function propise_plugin_section(&$sections){
	$sections['propise'] = 'Gestion des sondes de type propise';
}

function propise_plugin_action(){
	global $_,$conf;
	switch($_['action']){




		case 'propise_search':
			Action::write(function($_,&$response){
				global $myUser;
				if(!$myUser->can('propise','read')) throw new Exception("Permissions insuffisantes");
				require_once('Sensor.class.php');
				foreach(Sensor::loadAll()as $sensor){
					$sensor->location = Room::getById($sensor->location);

					
			    	$sensor = $sensor->toArray();
			   		$sensor['link'] =  ROOT_URL.'/action.php?action=propise_add_data&id='.$sensor['id'].'&light={{LIGHT}}&humidity={{HUMIDITY}}&temperature={{TEMPERATURE}}&mouvment={{MOUVMENT}}';
					$response['rows'][] = $sensor;
				}
					
			});
		break;
		
		case 'propise_save':
			Action::write(function($_,&$response){
				global $myUser;
				if(!$myUser->can('propise','edit')) throw new Exception("Permissions insuffisantes");
				require_once('Sensor.class.php');
				$sensor = isset($_['id']) ? Sensor::getById($_['id']) : new Sensor();
				if(!isset($_['label']) || empty($_['label'])) throw new Exception("Nom obligatoire");
				$sensor->label = $_['label'];
				$sensor->location = $_['location'];
				$sensor->save();
			});
		break;
		
		case 'propise_edit':
			Action::write(function($_,&$response){
				global $myUser;
				if(!$myUser->can('propise','edit')) throw new Exception("Permissions insuffisantes");
				require_once('Sensor.class.php');
				$sensor = Sensor::getById($_['id']);
				$response = $sensor;
			});
		break;

		case 'propise_delete':
			Action::write(function($_,&$response){
				global $myUser;
				if(!$myUser->can('propise','delete')) throw new Exception("Permissions insuffisantes");
				require_once('Sensor.class.php');
				Sensor::deleteById($_['id']);
			});
		break;

		case 'propise_widget_configure':
			Action::write(function($_,&$response){
				require_once(__DIR__.SLASH.'Sensor.class.php');
				$response['content'] = '<h4><i class="fa fa-wrench"></i> Configuration capteur</h4>'; 
				$response['content'] .= 'Sélectionnez le capteur à lier à ce widget :';
				$response['content'] .= '<select id="sensor">';
				foreach(Sensor::loadAll() as $sensor){
					$response['content'] .= '<option value="'.$sensor->id.'">'.$sensor->label.'</option>';
				}
				$response['content'] .= '</select>';
			});
		break;

		case 'propise_select_widget_menu':
			Action::write(function($_,&$response){
				require_once(__DIR__.SLASH.'Sensor.class.php');
				$widget = Widget::getById($_['id']);
				$widget->data('menu',$_['menu']);
				$widget->save();
			});
		break;

		case 'propise_widget_load':
			$widget = Widget::current();
			$id = $widget->data('sensor');
			
			if($id==''){
				$content = '
				<div class="propise-no-configuration">
				<h4><i class="fa fa-adjust"></i> Aucun capteur configuré</h4>
				<p>Cliquez sur la clé à molette pour configurer ce widget</p>
				</div>';
				
			}else{
				require_once('Sensor.class.php');
				require_once('Data.class.php');
				$sensor =  Sensor::getById($id);
				$data = Data::load(array('sensor'=>$sensor->id),'id DESC');
				$parameters = array(
					'menu' => ''
				);
				$content = '<div class="propise_widget" data-view="'.$widget->data('menu').'" data-id="'.$sensor->id.'">';
				$content .= '<div class="propise_view">
								<ul>
									<li data-type="light"><i class="fa fa-sun-o fa-spin-low"></i> <span >'.$data->light.'</span>%</li>
									<li data-type="temperature"><i class="fa fa-fire"></i> <span>'.$data->temperature.'</span>°</li>
									<li data-type="humidity"><i class="fa fa-tint"></i> <span>'.$data->humidity.'</span>%</li>
									<li data-type="mouvment"><i class="fa fa-eye"></i> <span>'.$data->mouvment.'</span></li>
								</ul>
								<div class="clear"></div>';
				$content .= '</div>';
				
				$content .= '<ul class="propise_menu">';
					$content .= '<li class="propise_global" onclick="propise_menu(this)" data-view=""><i class="fa fa-columns"></i></li>';
					$content .= '<li onclick="propise_menu(this)" data-view="light"><i class="fa fa-sun-o"></i></li>';
					$content .= '<li onclick="propise_menu(this)" data-view="temperature"><i class="fa fa-fire"></i></li>';
					$content .= '<li onclick="propise_menu(this)" data-view="humidity"><i class="fa fa-tint"></i></li>';
					$content .= '<li onclick="propise_menu(this)" data-view="mouvment"><i class="fa fa-eye"></i></li>';
					//$content .= '<li onclick="window.open(\'index.php?module=propise&section=stats&id='.$sensor->id.'\')" data-view="stats"><i class="fa fa-line-chart"></i></li>';
				$content .= '</ul>';		
				$content .= '</div>';

			}

			$widget->content =  $content;
			echo json_encode($widget);
		break;

	}
}


function propise_plugin_widget_refresh(&$response){
	require_once(__DIR__.SLASH.'Sensor.class.php');
	require_once(__DIR__.SLASH.'Data.class.php');
	$widgets = Widget::loadAll(array('model'=>'propise'));
	foreach($widgets as $widget){
		if(!is_numeric($widget->data('sensor'))) continue;
		$sensor = Sensor::getById($widget->data('sensor'));
		$data = Data::load(array('sensor'=>$sensor->id));
		$response[$widget->id]['callback'] = 'propise_refresh';
		if($data!=false)
			$response[$widget->id]['data'] = $data->toArray();

		$response[$widget->id]['widget']['title'] = $sensor->label.' ('.date('H\hi:s\s').')';
		//$response[3]['widget']['icon'] = 'fa-user';
		//$response[$widget->id]['widget']['background'] = '#'.rand(0,6).rand(0,6).rand(0,6).rand(0,6).rand(0,6).rand(0,6);
	}
	
	
}

function propise_plugin_widget(&$widgets){
	$modelWidget = new Widget();
	$modelWidget->model = 'propise';
	$modelWidget->title = 'Sonde';
	$modelWidget->icon = 'fa-tint';
	$modelWidget->background = '#50597B';
	$modelWidget->load = 'action.php?action=propise_widget_load';
	$modelWidget->configure = 'action.php?action=propise_widget_configure';
	$modelWidget->delete = 'action.php?action=propise_widget_delete';
	$modelWidget->js = [Plugin::url().'/main.js'];
	$modelWidget->css = [Plugin::url().'/main.css'];
	$widgets[] = $modelWidget;
}


function propise_setting_menu(&$settingMenu){
	global $_,$myUser;
	if(!$myUser->can('propise','configure')) return;
	$settingMenu[]= array(
		'sort' =>1,
		'url' => 'setting.php?section=propise',
		'icon' => 'angle-right',
		'label' => 'Sondes (Propise)'
	);
}

function propise_setting_page(){
	global $_,$myUser;
	if($_['section'] != 'propise' || !$myUser->can('propise','configure')) return;
	require_once(__DIR__.SLASH.'setting.php');
}



Plugin::addCss("/main.css"); 
Plugin::addJs("/main.js"); 

Plugin::addHook("install", "propise_plugin_install");
Plugin::addHook("uninstall", "propise_plugin_uninstall"); 
Plugin::addHook("section", "propise_plugin_section");  
Plugin::addHook("action", "propise_plugin_action");    
Plugin::addHook("widget", "propise_plugin_widget");    
Plugin::addHook("widget_refresh", "propise_plugin_widget_refresh"); 
Plugin::addHook("menu_setting", "propise_setting_menu");
Plugin::addHook("content_setting", "propise_setting_page");

?>