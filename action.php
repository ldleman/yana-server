<?php


if(!ini_get('safe_mode')) @set_time_limit(0);

require_once("common.php");
if(php_sapi_name() == 'cli'){
	$_['action'] = $_SERVER['argv'][1];	
}

$response = array();

Plugin::callHook("action_pre_case", array(&$_,$myUser));



if(!isset($myUser) && isset($_['token'])){
	$userManager = new User();
	$myUser = $userManager->load(array('token'=>$_['token']));
}


//Execution du code en fonction de l'action
switch ($_['action']){
	case 'login':
			$user = $userManager->exist($_['login'],$_['password']);
			$error = '';
			if($user==false){
				$error = '?error='.urlencode('le compte spécifié est inexistant');
			}else{
				$_SESSION['currentUser'] = serialize($user);
			}
			header('location: ./index.php'.$error);	
	break;
	
	case 'user_add_user':
		if(!$myUser->can('user','c')) exit('ERREUR: Permissions insuffisantes.');
		$user = new User();
		$user->setMail($_['mailUser']);
		$user->setName($_['nameUser']);
		$user->setFirstName($_['firstNameUser']);
		$user->setPassword($_['passwordUser']);
		$user->setLogin($_['loginUser']);
		$user->setRank($_['rankUser']);
		$user->setState(1);
		$user->save();
		header('location:setting.php?section=user');
	break;

	case 'delete_user':
		if(!$myUser->can('user','d')) exit('ERREUR: Permissions insuffisantes.');
		$userManager = new User();
		if(isset($_['id'])){
			$userManager->delete(array('id'=>$_['id']));
		}
		header('location:setting.php?section=user');
	break;


	case 'access_delete_rank':
		$rankManager = new Rank();
		$rankManager->delete(array('id'=>$_['id']));
		header('location:setting.php?section=access');
	break;

	case 'access_add_rank':
		$rank = new Rank();
		$rank->setLabel($_['labelRank']);
		$rank->setDescription($_['descriptionRank']);
		$rank->save();
		header('location:setting.php?section=access');
	break;


	case 'set_rank_access':
		$right = new Right();

		$right = $right->load(array('section'=>$_['section'],'rank'=>$_['rank']));

		$right = (!$right?new Right():$right);

		$right->setSection($_['section']);

		$_['state'] = ($_['state']==1?true:false);

		switch($_['access']){
			case 'c':
				$right->setCreate($_['state']);
			break;
			case 'r':
				$right->setRead($_['state']);
			break;
			case 'u':
				$right->setUpdate($_['state']);
			break;
			case 'd':
				$right->setDelete($_['state']);
			break;
		}
		$right->setRank($_['rank']);
		$right->save();

	break;

	case 'access_delete_right':
		$rankManager = new Right();
		$rankManager->delete(array('id'=>$_['id']));
		header('location:setting.php?section=right&id='.$_['rankRight']);
	break;

	case 'logout':
		$_SESSION = array();
		session_unset();
		session_destroy();
		header('location: ./index.php');
	break;

	case 'changePluginState':
		if($myUser==false) exit('Vous devez vous connecter pour cette action.');
		if(!$myUser->can('plugin','u')) exit('ERREUR: Permissions insuffisantes.');
		if($_['state']=='0'){
			Plugin::enabled($_['plugin']);

		}else{
			Plugin::disabled($_['plugin']);
		}
		header('location: ./setting.php?section=plugin');
	break;

	case 'crontab':
		Plugin::callHook("cron", array());
	break;
	
	default:
		Plugin::callHook("action_post_case", array());
	break;

	case 'GET_SPEECH_COMMAND':
		$actionUrl = 'http://'.$_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
		$actionUrl = substr($actionUrl,0,strpos($actionUrl , '?'));
	
		Plugin::callHook("vocal_command", array(&$response,$actionUrl));

		$json = json_encode($response);
		echo ($json=='[]'?'{}':$json);
	break;

	case 'GET_EVENT':
	$response = array('responses'=>array());
	Plugin::callHook("get_event", array(&$response));
		/*$response = array('responses'=>array(
							//array('type'=>'command','program'=>'"C:\Program Files (x86)\Notepad++\notepad++.exe"'),
							array('type'=>'talk','sentence'=>'Je lancerais le programme notepade plusse plusse, après la notification sonore.','style'=>'angry'),
							array('type'=>'talk','sentence'=>'Je lancerais le programme notepade plusse plusse, après la notification sonore.','style'=>'sad'),
							array('type'=>'talk','sentence'=>'Je lancerais le programme notepade plusse plusse, après la notification sonore.'),
							//array('type'=>'sound','file'=>'pet.wav')
						)
					  );*/


		$json = json_encode($response);
		echo ($json=='[]'?'{}':$json);
	break;



	
	default:
		Plugin::callHook("action_post_case", array());
	break;
}


?>
