<?php
/*
@name IpCam
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@description Visualisation en streaming de camera(s) IP
*/




function ipcam_action(){
	global $_,$conf;

	switch($_['action']){


		case 'ipcam_save_camera':
			
			Action::write(
				function($_,&$response){

					
					require_once('Camera.class.php');
					$camera = new Camera();

					if(empty($_['labelCamera'])) throw new Exception("Le nom est obligatoire");
					if(empty($_['ipCamera']))  throw new Exception("L'IP est obligatoire");

					$camera = !empty($_['id']) ? $camera->getById($_['id']): new Camera();
					$camera->label = $_['labelCamera'];
					$camera->location = $_['locationCamera'];
					$camera->ip  = $_['ipCamera'];
					$camera->login  = $_['loginCamera'];
					$camera->password  = $_['passwordCamera'];
					$camera->save();
					
					
					$response['message'] = 'Caméra enregistrée avec succès';
				},
				array('ipcam'=>'c')
			);
		break;

		case 'ipcam_delete_camera':
			Action::write(
				function($_,$response){

					require_once('Camera.class.php');
					$camera = new Camera();
					$camera->delete(array('id'=>$_['id']));
				},
				array('ipcam'=>'d') 
			);
		break;


		
		case 'ipcam_load_widget':

			require_once(dirname(__FILE__).'/../dashboard/Widget.class.php');
			
			Action::write(
				function($_,&$response){	

					$widget = new Widget();
					$widget = $widget->getById($_['id']);
					$parameters = $widget->data();

					if(empty($parameters['camera'])){
						$content = 'Choisissez une camera en cliquant sur l \'icone <i class="fa fa-wrench"></i> de la barre du widget';
					}else{
						
						
						global $conf;
						
						require_once('Camera.class.php');
						$camera  = new Camera();
					
						$camera = $camera->getById($parameters['camera']);
					
					
						
						$room = new Room();
						$room = $room->getById($camera->location);

						$response['title'] = 'Sonde '.$camera->label.' ('.$room->getName().')';


						$content = '
						<!-- CSS -->
						<style>
						</style>
						
						<!-- HTML -->
						';
						
						$content .= '<div class="ipcam_widget"><img name="main" id="main" border="0" src="http://'.$camera->login.':'.$camera->password.'@'.$camera->ip.'/videostream.cgi">';
						$content .= '</div>';
						$content .= '
						<!-- JS -->
						<script type="text/javascript">
						</script>
						';
					}
					$response['content'] = $content;
				}
			);
		break;

		case 'ipcam_edit_widget':
			require_once(dirname(__FILE__).'/../dashboard/Widget.class.php');
			require_once(dirname(__FILE__).'/Camera.class.php');
			$widget = new Widget();
			$widget = $widget->getById($_['id']);
			$data = $widget->data();
			$camera = new Camera();
			$cameras = $camera->populate();
			$content = '<h3>Choix de la camera</h3>';
			if(count($cameras) == 0):
			$content = 'Aucune camera enregistrée,<a href="setting.php?section=ipcam">enregistrer une camera<a>';
			else :
			$content .= '<select id="camera">';
			foreach($cameras as $camera)
				$content .= '<option '.($camera->id==$data['camera']?'selected="selected"':'').' value="'.$camera->id.'">'.$camera->label.' ('.$camera->uid.')</option>';

			$content .= '</select>';
			endif;
			echo $content;
		break;

		case 'ipcam_save_widget':
			require_once(dirname(__FILE__).'/../dashboard/Widget.class.php');
			$widget = new Widget();
			$widget = $widget->getById($_['id']);
			$data = $widget->data();
			
			$data['camera'] = $_['camera'];
			$widget->data($data);
			$widget->save();
			echo $content;
		break;
	}

}



function ipcam_plugin_setting_page(){
	global $_,$myUser,$conf;
	if(isset($_['section']) && $_['section']=='ipcam' ){
		
			require_once('Camera.class.php');

		if(!$myUser) throw new Exception('Vous devez être connecté pour effectuer cette action');
		$cameraManager = new Camera();
		$cameras = $cameraManager->populate();
		$roomManager = new Room();
		$rooms = $roomManager->populate();
		$selected =  new Camera();

		//Si on est en mode modification
		if (isset($_['id']))
			$selected = $cameraManager->getById($_['id']);
			

		
		?>

		<div class="span9 userBloc">

			<h1>Camera</h1>
			<p>Gestion des cameras IP</p>  

			<fieldset>
			    <legend>Ajouter/Modifier une camera</legend>

			    <div class="left">

				    <label for="labelCamera">Nom</label>
				    <input type="hidden" id="id" value="<?php echo $selected->id; ?>">
				    <input type="text" id="labelCamera" value="<?php echo $selected->label; ?>" placeholder="Sonde du salon"/>
			
				    <label for="ipCamera">IP</label>
				    <input type="text" value="<?php echo $selected->ip; ?>" id="ipCamera" placeholder="192.168.11.27:87" />
				    
					<label for="loginCamera">Login</label>
				    <input type="text" value="<?php echo $selected->login; ?>" id="loginCamera" placeholder="" />
				    
				    <label for="passwordCamera">Password</label>
				    <input type="text" value="<?php echo $selected->password; ?>" id="passwordCamera" placeholder="" />
				    
				    <label for="locationCamera">Pièce de la maison</label>
				    <select id="locationCamera">
				    	<?php foreach($rooms as $room){ ?>
				    	<option <?php if ($selected->location == $room->getId()){echo "selected";} ?> value="<?php echo $room->getId(); ?>"><?php echo $room->getName(); ?></option>
				    	<?php } ?>
				    </select>
				   
				</div>

	  			<div class="clear"></div>
			    <br/><button onclick="plugin_ipcam_save(this)" class="btn">Enregistrer</button>
		  	</fieldset>
			<br/>


			<fieldset>
				<legend>Consulter les sondes existants</legend>
				<table class="table table-striped table-bordered table-hover">
				    <thead>
					    <tr>
					    	<th>Nom</th>
						    <th>IP</th>
						    <th>Pièce</th>
						    <th colspan="2"></th>
						    
					    </tr>
				    </thead>
			    
			    	<?php foreach($cameras as $camera){ 
			    		$room = $roomManager->load(array('id'=>$camera->location)); 
			    	?>
					<tr>
				    	<td><?php echo $camera->label; ?></td>
					    <td><?php echo $camera->ip; ?></td>
					    <td><?php echo $room->getName(); ?></td>
					    <td>
					    	<a class="btn" href="setting.php?section=ipcam&id=<?php echo $camera->id; ?>"><i class="fa fa-pencil"></i></a>
					    	<div class="btn" onclick="plugin_ipcam_delete(<?php echo $camera->id; ?>,this);"><i class="fa fa-times"></i></div>
					    </td>
					    </td>
			    	</tr>
			    <?php } ?>
			    </table>
			</fieldset>
		</div>

<?php
	}
}

function ipcam_plugin_setting_menu(){
	global $_;
	echo '<li '.(isset($_['section']) && $_['section']=='ipcam'?'class="active"':'').'><a href="setting.php?section=ipcam"><i class="fa fa-angle-right"></i> Caméra IP</a></li>';
}




function ipcam_plugin_widget(&$widgets){
		$widgets[] = array(
		    'uid'      => 'dash_ipcam',
		    'icon'     => 'fa fa-video-camera',
		    'label'    => 'Camera',
		    'background' => '#C511D6', 
		    'color' => '#fffffff',
		    'onLoad'   => 'action.php?action=ipcam_load_widget',
		    'onEdit'   => 'action.php?action=ipcam_edit_widget',
		    'onSave'   => 'action.php?action=ipcam_save_widget',
		);
}



Plugin::addJs("/js/main.js");
//Lie wireRelay_plugin_setting_page a la zone réglages
Plugin::addHook("setting_bloc", "ipcam_plugin_setting_page");
//Lie wireRelay_plugin_setting_menu au menu de réglages
Plugin::addHook("setting_menu", "ipcam_plugin_setting_menu"); 
Plugin::addHook("action_post_case", "ipcam_action");    
Plugin::addHook("widgets", "ipcam_plugin_widget");

?>
