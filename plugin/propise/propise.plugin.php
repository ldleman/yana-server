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
	$widget = Widget::getById(1);
	$widget->content = 'Dernier rafraichissement : '.date('d/m/Y H:i:s');
	$widgets[] = $widget ;
}

function propise_plugin_widget(&$widgets){
	$modelWidget = new Widget();
	$modelWidget->model = 'propise';
	$modelWidget->title = 'Sonde';
	$modelWidget->icon = 'fa-tint';
	$modelWidget->background = '#ffffff';
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