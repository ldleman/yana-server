<?php


if(!ini_get('safe_mode')) @set_time_limit(0);

require_once("common.php");
if(php_sapi_name() == 'cli'){
	$_['action'] = $_SERVER['argv'][1];	
}

$response = array();

Plugin::callHook("action_pre_case", array(&$_,$myUser));



if(!$myUser && isset($_['token'])){
	$userManager = new User();
	$myUser = $userManager->load(array('token'=>$_['token']));
	if(isset($myUser) && $myUser!=false)
	$myUser->loadRight();
}
$myUser = (!$myUser?new User():$myUser);

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

			if(isset($_['rememberMe'])){
				setcookie(COOKIE_NAME, $user->coockie(), mktime(0,0,0, date("d"),date("m"), (date("Y")+1)),'/');
			}


			header('location: ./index.php'.$error);	
	break;

	case 'GET_TOKEN':
			$user = $userManager->load(array('login'=>$_['login'],'password'=>sha1(md5($_['password']))));
			$response['token'] = $user->getToken();
			echo json_encode($response);
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
		$user->setToken(sha1(time().rand(0,1000)));
		$user->save();
		header('location:setting.php?section=user');
	break;

	case 'delete_user':
		if(!$myUser->can('user','d')) exit('ERREUR: Permissions insuffisantes.');
		$userManager = new User();
		$NbUsers = $userManager->rowCount();
		
		if(isset($_['id']) && $NbUsers > 1){
			$userManager->delete(array('id'=>$_['id']));
			header('location:setting.php?section=user');
		}
		else
		{
		header('location:setting.php?section=user&error=Impossible de supprimer le dernier utilisateur.');
		}
	break;


	case 'access_delete_rank':
		if(!$myUser->can('configuration','d')) exit('ERREUR: Permissions insuffisantes.');
		$rankManager = new Rank();
		$rankManager->delete(array('id'=>$_['id']));
		header('location:setting.php?section=access');
	break;

	case 'access_add_rank':
		if(!$myUser->can('configuration','c')) exit('ERREUR: Permissions insuffisantes.');
		$rank = new Rank();
		$rank->setLabel($_['labelRank']);
		$rank->setDescription($_['descriptionRank']);
		$rank->save();
		header('location:setting.php?section=access');
	break;


	case 'set_rank_access':
		if(!$myUser->can('configuration','c')) exit('ERREUR: Permissions insuffisantes.');
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

	if(!$myUser->can('configuration','d')) exit('ERREUR: Permissions insuffisantes.');
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
		if(!$myUser->can('vocal','r')) exit('{"error":"insufficient permissions"}');
		$actionUrl = 'http://'.$_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
		$actionUrl = substr($actionUrl,0,strpos($actionUrl , '?'));
	
		Plugin::callHook("vocal_command", array(&$response,$actionUrl));

		$json = json_encode($response);
		echo ($json=='[]'?'{}':$json);
	break;


	case 'GET_EVENT':
	if(!$myUser->can('vocal','r')) exit('{"error":"insufficient permissions"}');
	$response = array('responses'=>array());
	Plugin::callHook("get_event", array(&$response));

		$json = json_encode($response);
		echo ($json=='[]'?'{}':$json);
	break;



	
	default:
		Plugin::callHook("action_post_case", array());
	break;
}


?>
