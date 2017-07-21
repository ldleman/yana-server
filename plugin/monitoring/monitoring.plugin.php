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
		case 'monitoring_widget_profile_load':
			global $myUser;
			$widget = Widget::current();
			$widget->title = 'Profile';
			$widget->content = '<div class="profileContainer">
				<div class="profileHeader"></div>
				<a href="account.php" class="profileImage"><img class="avatar-mini" src="'.$myUser->getAvatar().'"></a>
				<h3>'.$myUser->fullname().'</h3>
				<small>'.$myUser->rank_object->label.'</small>
				<div class="profileToken">Token : <input onclick="$(this).select();" type="text" value="'.$myUser->token.'"></div>
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
	$modelWidget->js = [Plugin::url().'/main.js'];
	$modelWidget->css = [Plugin::url().'/main.css'];
	$modelWidget->description = "Affiche l'heure en temps réel";
	$widgets[] = $modelWidget;


	$modelWidget = new Widget();
	$modelWidget->model = 'profile';
	$modelWidget->title = 'Profile';
	$modelWidget->icon = 'fa-user';
	$modelWidget->background = '#50597b';
	$modelWidget->load = 'action.php?action=monitoring_widget_profile_load';
	$modelWidget->js = [Plugin::url().'/main.js'];
	$modelWidget->css = [Plugin::url().'/main.css'];
	$modelWidget->description = "Affiche les informations de profil";
	$widgets[] = $modelWidget;
}

function monitoring_cron_action(){

	if(date('i:s')=='00:00')
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