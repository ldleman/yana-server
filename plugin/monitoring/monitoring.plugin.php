<?php



function monitoring_plugin_install($id){
	if($id != 'fr.idleman.monitoring') return;
}
function monitoring_plugin_uninstall($id){
	if($id != 'fr.idleman.monitoring') return;

}

function monitoring_plugin_section(&$sections){
	$sections['monitoring'] = 'Gestion du plugin Monitoring';
}

function monitoring_plugin_action(){
	global $_,$conf;
	switch($_['action']){
		case 'monitoring_widget_clock_load':
			$widget = Widget::current();
			$widget->title = 'Horloge';
			$widget->content = '<div class="clockContainer">
			<div class="clock" id="clock"></div>
			</div>';
			echo json_encode($widget);
		break;
	}
}



function monitoring_plugin_widget(&$widgets){
	$modelWidget = new Widget();
	$modelWidget->model = 'clock';
	$modelWidget->title = 'Horloge';
	$modelWidget->icon = 'fa-clock-o';
	$modelWidget->background = '#50597b';
	$modelWidget->load = 'action.php?action=monitoring_widget_clock_load';
	$modelWidget->delete = 'action.php?action=monitoring_widget_clock_delete';
	$modelWidget->js = [Plugin::url().'/main.js'];
	$modelWidget->css = [Plugin::url().'/main.css'];
	$widgets[] = $modelWidget;
}

function monitoring_cron_action(){

	//if(date('i:s')=='00:00')
		print_r(Client::talk('Il est '.date('H').'heure.'));
}

Plugin::addCss("/main.css"); 
Plugin::addJs("/main.js"); 

Plugin::addHook("install", "monitoring_plugin_install");
Plugin::addHook("uninstall", "monitoring_plugin_uninstall"); 
Plugin::addHook("section", "monitoring_plugin_section");  
Plugin::addHook("action", "monitoring_plugin_action");    
Plugin::addHook("widget", "monitoring_plugin_widget");
Plugin::addHook("cron", "monitoring_cron_action");    
 

?>