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
		case 'monitoring_widget_log_load':
			global $myUser;
			if(!$myUser->can('log','read')) throw new Exception("Permissions insuffisantes");
			$widget = Widget::current();
			$logs = Log::loadAll(array(),array('date DESC'),array(30));

			$widget->title = '30 derniers logs';
			$widget->content = '<table class="table table-stripped table-hover">';
				
			$widget->content .= '<tr><th style="width:90px">Date</th><th>Libellé</th><th>Utilisateur</th></tr>';
			foreach($logs as $log){
				$widget->content .= '<tr><td><i class="fa fa-calendar-o"></i> '.date('d-m-y',$log->date).'<i class="fa fa-clock-o"></i> '.date('H:i:s',$log->date).'</td><td>'.$log->label().'</td><td><i class="fa fa-user"></i> '.$log->user.'</td></tr>';
			}
			$widget->content .= '</ul>';

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
	global $myUser;

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



	$modelWidget = new Widget();
	$modelWidget->model = 'log';
	$modelWidget->title = 'Logs';
	$modelWidget->width = 8;
	$modelWidget->options[] = array('function'=>'window.location = \'setting.php?section=log\';','icon'=>'fa-eye','label'=>'Voir tous les logs');
	$modelWidget->icon = 'fa-commenting-o';
	$modelWidget->background = '#50597b';
	$modelWidget->load = 'action.php?action=monitoring_widget_log_load';
	$modelWidget->js = [Plugin::url().'/main.js'];
	$modelWidget->css = [Plugin::url().'/main.css'];
	$modelWidget->description = "Affiche les informations des 30 derniers logs";
	if($myUser->can('log','read')) 
		$widgets[] = $modelWidget;
}

function monitoring_cron_action(){

	//if(date('i:s')=='00:00')
//		print_r(Client::talk('Il est '.date('H').'heure.'));
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