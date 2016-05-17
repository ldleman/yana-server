<?php
/*
@name Dashboard
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@description Active la dashboard dynamique utilisable par d'autres plugins
@type plugin
*/



function dashboard_plugin_actions(){

	global $myUser,$_,$conf;
	switch($_['action']){
		case 'GET_WIDGETS':
			header('Content-type: application/json');

			require_once(dirname(__FILE__).'/Dashboard.class.php');
			require_once(dirname(__FILE__).'/Widget.class.php');

			$dashManager = new Dashboard();
			$dashManager->change(array('default'=>'0'),array('user'=>$myUser->getId()));
			$dashManager->change(array('default'=>'1'),array('id'=>$_['dashboard'],'user'=>$myUser->getId()));

			$widgetManager = new Widget();
			$model = array();
			Plugin::callHook("widgets",array(&$model));


			$widgets = $widgetManager->loadAll(array('dashboard'=>$_['dashboard']),'cell');
			$data = array();



			foreach($widgets as $widget){
				if(!is_active_model($widget->model,$model)) continue;
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
			require_once(dirname(__FILE__).'/Widget.class.php');

			$response = array();

			$widget = new Widget();
			$widget->data = json_encode($_POST['data']);
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
			require_once(dirname(__FILE__).'/Widget.class.php');
			$response = array();
			$widgetManager = new Widget();
			$widgetManager = $widgetManager->getById($_['id']);
			$widgetManager->minified = 1;
			$widgetManager->save();

			echo json_encode($response);
		break;
		case 'MAXIMIZE_WIDGET':
			header('Content-type: application/json');
			require_once(dirname(__FILE__).'/Widget.class.php');
			$response = array();
			$widgetManager = new Widget();
			$widgetManager = $widgetManager->getById($_['id']);
			$widgetManager->minified = 0;
			$widgetManager->save();

			echo json_encode($response);
		break;

		case 'MOVE_WIDGET':
			header('Content-type: application/json');
			require_once(dirname(__FILE__).'/Widget.class.php');
			$response = array();
			$widgetManager = new Widget();

			foreach($_['sort']['cells'] as $id=>$sort){
				if(empty($sort)) continue;
				$widgetManager->change(array('cell'=>$sort['cell'],'column'=>$sort['column']),array('id'=>$id));
			}
			
			echo json_encode($response);
		break;

		case 'DELETE_WIDGET':
			header('Content-type: application/json');
			require_once(dirname(__FILE__).'/Widget.class.php');
			$response = array();

			$widgetManager = new Widget();
			$widgetManager->delete(array('id'=>$_['id']));

			echo json_encode($response);
		break;

		case 'DASH_ADD_VIEW':
			global $_,$myUser;
			require_once(dirname(__FILE__).'/Dashboard.class.php');
			$entity = new Dashboard();
			$entity->user = $myUser->getId();
			$entity->label = $_['viewName'];
			$entity->default = 0;
			$entity->save();
			header('location: setting.php?section=preference&block=dashboard');
		break;
		case 'DASH_DELETE_VIEW':
			global $_,$myUser;
			require_once(dirname(__FILE__).'/Dashboard.class.php');
			$entity = new Dashboard();
			$entity->delete(array('id'=>$_['id']));
			header('location: setting.php?section=preference&block=dashboard');
		break;
	}
}

function dashboard_plugin_home(){
	global $_,$myUser;
	if(!isset($_['module'])){
		require_once(dirname(__FILE__).'/Dashboard.class.php');
		require_once(dirname(__FILE__).'/Widget.class.php');
		$dashManager = new Dashboard();
		$dashes = $dashManager->loadAll(array('user'=>$myUser->getId()),'label');
		
		if(count($dashes) == 0){
			Dashboard::initForUser($myUser->getId());
			$dashes = $dashManager->loadAll(array('user'=>$myUser->getId()),'label');
		}
		//var_dump($dashes);
		echo '<div style="margin:0;text-align:center;"><select id="dashboard_switch" onchange="plugin_dashboard_load_view($(this).val());"><option value="">-</option>';
		foreach($dashes as $dash){
			echo '<option '.($dash->default=='1'?'selected="selected"':'').' value="'.$dash->id.'">'.$dash->label.'</option>';
		}
		echo '</select></div>';
		echo '<div id="dashboard"></div>';
	}
}



function dashboard_plugin_preference_menu(){
	global $_;
	echo '<li '.(@$_['block']=='dashboard'?'class="active"':'').'><a  href="setting.php?section=preference&block=dashboard"><i class="fa fa-angle-right"></i> Dashboard</a></li>';
}

function dashboard_plugin_preference_page(){
	global $myUser,$_,$conf;
	if((isset($_['section']) && $_['section']=='preference' && @$_['block']=='dashboard' )  ){
		if($myUser!=false){


			require_once(dirname(__FILE__).'/Dashboard.class.php');
			$dashManager = new Dashboard();
			$dashes = $dashManager->loadAll(array('user'=>$myUser->getId()));
		
		

	?>

		<div class="span9 userBloc">
		<legend>Dashboard disponibles</legend>

		<form style="margin:0;" action="action.php?action=DASH_ADD_VIEW" method="POST">
			<div class="input-append">
				<input type="text" name="viewName" placeholder="Salon,cuisine...">
				<button type="submit" class="btn">Ajouter la vue</button>
			</div>
		</form>
	<table class="table table-striped table-bordered">
		<tr>
			<th>Nom</th>
			<th>Options</th>
		</tr>
	<?php	foreach($dashes as $dash){ ?>
			<tr class="command" data-id="<?php echo $dash->id; ?>">
				<td><?php echo $dash->label; ?></td>
				<td><a class="btn" href="action.php?action=DASH_DELETE_VIEW&id=<?php echo $dash->id; ?>"><i class="fa fa-times"></i></a></td>
			</tr>
	<?php	}  ?>
		<tr>
			<td colspan="3"><div class="btn" onclick="plugin_vocalinfo_save();">Enregistrer</div></td>
		</tr>
	</table>
		
		
		</div>

<?php }else{ ?>

		<div id="main" class="wrapper clearfix">
			<article>
					<h3>Vous devez être connecté</h3>
			</article>
		</div>
<?php

		}
	}
}


function is_active_model($model,$models){

	foreach($models as $m){
		
		
		if($m['uid'] == $model) return true;
	}
	return false;
}

Plugin::addJs('/js/jquery.dashboard.js',true);
Plugin::addJs('/js/main.js',true);
Plugin::addCss('/css/jquery.dashboard.css',true);

Plugin::addHook("home", "dashboard_plugin_home");
Plugin::addHook("action_post_case", "dashboard_plugin_actions");

Plugin::addHook("preference_menu", "dashboard_plugin_preference_menu"); 
Plugin::addHook("preference_content", "dashboard_plugin_preference_page"); 

?>
