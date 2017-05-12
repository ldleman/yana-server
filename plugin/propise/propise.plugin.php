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
		case 'propise_widget_load':
			$widget = Widget::current();
			$widget->content = 'popopoh';
			echo json_encode($widget);
		break;

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




	}
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

function propise_plugin_widget_refresh(&$widgets){

	$plugin_widgets = Widget::loadAll(array('model'=>'propise'));
	foreach($plugin_widgets as $plugin_widget){
		$plugin_widget->content = 'Humidité: '.rand(0,100);
		$widgets[] = $plugin_widget;
	}
}

function propise_plugin_widget(&$widgets){
	$modelWidget = new Widget();
	$modelWidget->model = 'propise';
	$modelWidget->title = 'Sonde';
	$modelWidget->icon = 'fa-tint';
	$modelWidget->background = '#222222';
	$modelWidget->load = 'action.php?action=propise_widget_load';
	$modelWidget->delete = 'action.php?action=propise_widget_delete';
	$modelWidget->js = [__DIR__.'/main.js'];
	$modelWidget->css = [__DIR__.'/main.css'];
	$widgets[] = $modelWidget;
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