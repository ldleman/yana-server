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

			require_once('Widget.class.php');
			$widgetManager = new Widget();

			$model = array();
			Plugin::callHook("widgets",array(&$model));

			$widgets = $widgetManager->populate('cell');
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
			$widget->data = json_encode($_['data']);
			$widget->column = $_['column'];
			$widget->cell = $_['cell'];
			$widget->model = $_['model'];
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
	}
}

function dashboard_plugin_home(){
	global $_;
	if(!isset($_['module'])){
		echo '<div id="dashboard"></div>';
	}
}

Plugin::addJs('/js/jquery.dashboard.js',true);
Plugin::addJs('/js/main.js',true);
Plugin::addCss('/css/jquery.dashboard.css',true);

Plugin::addHook("home", "dashboard_plugin_home");
Plugin::addHook("action_post_case", "dashboard_plugin_actions");
?>
