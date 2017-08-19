<?php



function relay_plugin_install($id){
	if($id != 'fr.idleman.relay') return;
	Entity::install(__DIR__);
	require_once('WireRelay.class.php');
	$relay =  new WireRelay();
	$relay->label = 'Interrupteur 1';
	$relay->icon = 'fa-lightbulb-o';
	$relay->type = 'wire';
	$relay->description = 'Interrupteur exemple';
	$relay->location = 1;
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
}

function relay_plugin_uninstall($id){
	if($id != 'fr.idleman.relay') return;
	Device::delete(array('plugin'=>'relay'));
	Widget::delete(array('model'=>'relay'));
	Entity::uninstall(__DIR__);
}

function relay_plugin_section(&$sections){
	$sections['relay'] = 'Gestion des relais filaires et radio';
}

function relay_plugin_action(){
	require_once(__DIR__.SLASH.'action.php');
}



function relay_plugin_widget(&$widgets){
	$modelWidget = new Widget();
	$modelWidget->model = 'relay';
	$modelWidget->title = 'Relais';
	$modelWidget->icon = 'fa-hand-grab-o';
	$modelWidget->background = '#34495e';
	$modelWidget->description = 'Permet la gestion des relais filaires et radio (interrupteurs,...)';
	$modelWidget->load = 'action.php?action=relay_widget_load';
	$modelWidget->configure = 'action.php?action=relay_widget_configure';
	$modelWidget->delete = 'action.php?action=relay_widget_delete';
	$modelWidget->js = [Plugin::url().'/main.js'];
	$modelWidget->css = [Plugin::url().'/main.css'];
	$widgets[] = $modelWidget;
}


function relay_add_types(&$types){
	$types['yana-radio-relay'] = array(
		'label'=>'Relais radio Yana',
		'uid'=>'yana-radio-relay',
		'handler' => __DIR__.SLASH.'RadioRelay.class.php'
	);
	$types['yana-wire-relay'] = array(
		'label'=>'Relais filaire Yana',
		'uid'=>'yana-wire-relay',
		'handler' => __DIR__.SLASH.'WireRelay.class.php'
	);
}

function relay_setting_menu(&$settingMenu){
	global $_,$myUser;
	if(!$myUser->can('relay','configure')) return;
	$settingMenu[]= array(
		'sort' =>1,
		'url' => 'setting.php?section=relay',
		'icon' => 'angle-right',
		'label' => 'Relais'
	);
}

function relay_setting_page(){
	global $_,$myUser;
	if($_['section'] != 'relay' || !$myUser->can('relay','configure')) return;
	require_once(__DIR__.SLASH.'setting.php');
}



Plugin::addCss("/main.css"); 
Plugin::addJs("/main.js"); 

Plugin::addHook("install", "relay_plugin_install");
Plugin::addHook("uninstall", "relay_plugin_uninstall"); 
Plugin::addHook("section", "relay_plugin_section");  
Plugin::addHook("action", "relay_plugin_action");    
Plugin::addHook("widget", "relay_plugin_widget");    
Plugin::addHook("menu_setting", "relay_setting_menu");
Plugin::addHook("content_setting", "relay_setting_page");
Plugin::addHook("relay_types", "relay_add_types");

?>