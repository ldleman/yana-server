<?php
/*
@name Dashboard
@author Valentin CARRUESCO <valentin.carruesco@sys1.fr>
@link http://www.sys1.fr
@licence Copyright Sys1
@version 1.0.0
@description Active la dashboard dynamique utilisable par d'autres plugins
@type plugin
*/



function dashboard_plugin_actions(){

	global $myUser,$_,$conf;
	switch($_['action']){
		case 'GET_WIDGETS':
			header('Content-type: application/json');
			
			require_once('Dashboard.class.php');
			require_once('Widget.class.php');

			$dashManager = new Dashboard();
			$dashManager->change(array('default'=>'0'));
			$dashManager->change(array('default'=>'1'),array('id'=>$_['dashboard']));

			$widgetManager = new Widget();

			$model = array();
			Plugin::callHook("widgets",array(&$model));

			$widgets = $widgetManager->loadAll(array('dashboard'=>$_['dashboard']),'cell');
			$data = array();
			foreach($widgets as $widget){
				$data[] = array('data'=>$widget->data,
								'column'=>$widget->column,
								'id'=>$widget->id,
								'cell'=>$widget->cell,
								'minified'=>$widget->minified,
								'model'=>$widget->model
								);
			}

			echo json_encode(array('model'=>$model,'data'=>$data));
		break;
		case 'ADD_WIDGET':

			header('Content-type: application/json');
			require_once('Widget.class.php');

			$response = array();

			$widget = new Widget();
			$widget->data = json_encode(array());
			$widget->column = $_['column'];
			$widget->cell = $_['cell'];
			$widget->model = $_['model'];
			$widget->dashboard = $_['view'];
			$widget->save();
			$response['id'] = $widget->id;

			echo json_encode($response);
		break;
		case 'MINIMIZE_WIDGET':

			header('Content-type: application/json');
			require_once('Widget.class.php');
			$response = array();
			$widgetManager = new Widget();
			$widgetManager = $widgetManager->getById($_['id']);
			$widgetManager->minified = 1;
			$widgetManager->save();

			echo json_encode($response);
		break;
		case 'MAXIMIZE_WIDGET':
			header('Content-type: application/json');
			require_once('Widget.class.php');
			$response = array();
			$widgetManager = new Widget();
			$widgetManager = $widgetManager->getById($_['id']);
			$widgetManager->minified = 0;
			$widgetManager->save();

			echo json_encode($response);
		break;

		case 'MOVE_WIDGET':
			header('Content-type: application/json');
			require_once('Widget.class.php');
			$response = array();
			$widgetManager = new Widget();
			$widgetManager = $widgetManager->getById($_['id']);

			$widgetManager->cell = $_['sort']['cell'];
			$widgetManager->column = $_['sort']['column'];
			$widgetManager->save();

			echo json_encode($response);
		break;

		case 'DELETE_WIDGET':
			header('Content-type: application/json');
			require_once('Widget.class.php');
			$response = array();

			$widgetManager = new Widget();
			$widgetManager->delete(array('id'=>$_['id']));

			echo json_encode($response);
		break;

		case 'DASH_ADD_VIEW':
			global $_,$myUser;
			require_once('Dashboard.class.php');
			$entity = new Dashboard();
			$entity->user = $myUser->getId();
			$entity->label = $_['viewName'];
			$entity->default = 0;
			$entity->save();
			header('location: index.php');
		break;
	}
}

function dashboard_plugin_home(){
	global $_,$myUser;
	if(!isset($_['module'])){
		require_once('Dashboard.class.php');
		$dashManager = new Dashboard();
		$dashes = $dashManager->loadAll(array('user'=>$myUser->getId()));
		
		
		echo '<form action="action.php?action=DASH_ADD_VIEW" method="POST">';
		echo '<select id="dashboard_switch" onchange="plugin_dashboard_load_view($(this).val());"><option value="">-</option>';
		foreach($dashes as $dash){
			echo '<option '.($dash->default=='1'?'selected="selected"':'').' value="'.$dash->id.'">'.$dash->label.'</option>';
		}
		echo '</select>
		<div class="input-append">
			<input type="text" name="viewName" placeholder="Salon,cuisine...">
			<button type="submit" class="btn">Ajouter la vue</button>
		</div>
		</form>
		';
		echo '<div id="dashboard"></div>';
	}
}

Plugin::addJs('/js/jquery.dashboard.js',true);
Plugin::addJs('/js/main.js',true);
Plugin::addCss('/css/jquery.dashboard.css',true);

Plugin::addHook("home", "dashboard_plugin_home");
Plugin::addHook("action_post_case", "dashboard_plugin_actions");
?>
