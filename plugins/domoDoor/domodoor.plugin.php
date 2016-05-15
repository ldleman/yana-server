<?php
/*
@name DomoDoor
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@type module
@description Plugin de gestion d'une porte bluetooth, accès par code + conditions fixées ans les réglages (cf tuto 22 blog.idleman.fr)
*/

if(isset($_GET['argv'])){
	$argv = explode('|',$_GET['argv']);
}

if(isset($argv)){

	require_once(dirname(__FILE__).'/../../constant.php');
	$db = new SQLite3(dirname(__FILE__).'/../../'.DB_NAME);
	
	$execQuery = $db->query('SELECT * FROM '.MYSQL_PREFIX.'plugin_door');
	

	while($queryReturn = $execQuery->fetchArray() ){
		$allowed_badges[] = $queryReturn['code'];
		$badges[$queryReturn['code']] = $queryReturn;
	}


	$response = 0;
	if(count($argv)>1){
		list($me,$badge) = $argv;
		$badge = trim(substr($badge,2,4));
		$user = 0;
		if(in_array($badge, $allowed_badges)){
			$response = 1;

			$user = $badges[$badge]['user'];
		}
		$execQuery = $db->exec('INSERT INTO '.MYSQL_PREFIX.'plugin_door_log(time,code,user,success)VALUES("'.time().'","'.$badge.'",'.$user.','.$response.')');
	}
	$db->close();
	echo $response;
	exit();
}

require_once(dirname(__FILE__).'/DoorAccess.class.php');
require_once(dirname(__FILE__).'/DoorLog.class.php');

function dash_domodoor_plugin_menu(&$widgets){
		$widgets[] = array(
		    'uid'      => 'dash_domodoor',
		    'icon'     => 'fa  fa-key',
		    'label'    => 'Porte d\'entréé',
		    'background' => '#50597B', 
		    'color' => '#fffffff',
		    'onLoad'   => 'action.php?action=domodoor_get_history',
		    'onDelete' => 'action.php?action=dash_domodoor_plugin_delete',
		);
}



function domodoor_plugin_setting_menu(){
	global $_;
	echo '<li '.(isset($_['section']) && $_['section']=='domodoor'?'class="active"':'').'><a href="setting.php?section=domodoor"><i class="fa fa-angle-right"></i> Porte domotique</a></li>';
	
}


function domodoor_plugin_setting_page(){
	global $myUser,$_;
	
	if(isset($_['section']) && $_['section']=='domodoor' ){
		if($myUser!=false){
			$doorAccessManager = new doorAccess();
			$accesses = $doorAccessManager->populate();
			$current = isset($_['id'])?$doorAccessManager->getById($_['id']): $doorAccessManager;
			$users = User::getAllUsers();
			?>

			<div class="span9 userBloc">


				<h1>Porte bluetooth</h1>
				<p>Gestion des accès à la porte</p>  

				<form action="action.php?action=domodoor_add_domodoor" method="POST"> 
					<fieldset>
						<legend><?php echo $description ?></legend>

						<div class="left">
							<label for="user">Utilisateur</label>
							<select id="user" name="user" >
								<option value="">-</option>
								<?php foreach($users as $user){ 
								echo '<option '.($user->getId()==$current->user?'selected="selected"':'').' value="'.$user->getId().'">'.$user->getFullName().'</option>';
								 } ?>
							</select>
							<label for="code">Code bluetooth (4 lettres)</label>
							<input type="text" value="<?php echo $current->code; ?>" maxlength="10" name="code" id="code" />
							<input type="hidden" name="id" id="id" value="<?php echo $_['id']; ?>">
						</div>

						<div class="clear"></div>
						<br/><button type="submit" class="btn">Enregistrer</button>
					</fieldset>
					<br/>
				</form>

				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th colspan="2">Utilisateur</th>
							<th>Code</th>
							<th></th> 
						</tr>
					</thead>

					<?php 
					$userManager = new User();
					foreach($accesses as $access){ 

						$user = $userManager->getById($access->user);
						?>
					<tr>
						<td><?php echo $user->getGravatarImg(30); ?></td>
						<td><?php echo $user->getFullName(); ?></td>
						<td><?php echo $access->code; ?></td>
						<td><a class="btn" href="action.php?action=domodoor_delete_domodoor&id=<?php echo $access->getId(); ?>"><i class="fa fa-times"></i></a>
							<a class="btn" href="setting.php?section=domodoor&id=<?php echo $access->getId(); ?>"><i class="fa fa-pencil"></i></a></td>
						</tr>
						<?php } ?>
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

	function domodoor_action_domodoor(){
		global $_,$myUser;

		switch($_['action']){

			case 'domodoor_add_domodoor':
				if(!$myUser->can('door','c')) exit('permission denied');
					$doorAccessManager = new DoorAccess();
					$doorAccess = new DoorAccess();
					$doorAccess =  $_['id']!='' ? $doorAccessManager->getById($_['id']) : $doorAccess ;
					$doorAccess->user = $_['user'];
					$doorAccess->code = str_pad($_['code'],4, "0");
					$doorAccess->save();
				
					header('location:setting.php?section=domodoor');			
			break;

			case 'domodoor_get_history':
				header('content-type: application/json');
				$response['title'] = 'Entrees porte';
				$doorAccessManager = new DoorLog();
				$morning = mktime (0, 0, 0, date("n") , date("j") ,  date("Y"));

				$entries = $doorAccessManager->loadAll(array('time'=>$morning),'time DESC',10,$operation=">");
				$response['content'] ='<ul class="domodoor_history">';
				$userManager = new User();
				foreach($entries as $entry){
					$user = new User();

					if($entry->user!=0){
						$user = $userManager->getById($entry->user);
					}
					$response['content'] .='<li title="Code fournis : '.$entry->code.'" class="domodoor_log state-'.$entry->success.'">'.$user->getGravatarImg(50).'<div><h1>'.$user->getFullName().'</h1><h2>'.date('à H:i \l\e d/m ',$entry->time).'<h2></div></li>';
				}
				$response['content'] .= '</ul>';
				echo json_encode($response);
				exit(0);
			break;

			case 'domodoor_delete_domodoor':
			if($myUser->can('door','d')){
				$doorAccessManager = new DoorAccess();
				$doorAccessManager->delete(array('id'=>$_['id']));
				header('location:setting.php?section=domodoor');
			}
			else
			{
				header('location:setting.php?section=domodoor&error=Vous n\'avez pas le droit de faire ça!');
			}

			break;

		}
	}


	function domodoor_dashboard(){
		global $_,$myUser;
		if($myUser->can('door','r')){
			?>
			<div class="flatBloc domodoor-bloc">
			    <h3><i class="fa fa-sign-in"></i> Porte principale</h3>
			    <div id="dash_domodoor"></div>
		    </div>
			<?php
		}
	}

		Plugin::addCss("/css/style.css"); 
		Plugin::addJs("/js/main.js"); 
		Plugin::addHook("setting_menu", "domodoor_plugin_setting_menu");  
		Plugin::addHook("setting_bloc", "domodoor_plugin_setting_page"); 
		Plugin::addHook("action_post_case", "domodoor_action_domodoor"); 
		Plugin::addHook("dashboard_pre_column2", "domodoor_dashboard"); 
		Plugin::addHook("widgets", "dash_domodoor_plugin_menu");

		?>
