<?php
if ($_GET['action'] == 'KNOCK_KNOCK_YANA') exit('1');


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
	global $conf;
	$user = $userManager->exist($_['login'],$_['password']);
	$error = '?init=1';
	if($user==false){
		$error .= '&error='.urlencode('le compte spécifié est inexistant');
	}else{
		$_SESSION['currentUser'] = serialize($user);
	

	if(isset($_['rememberMe'])){	
		$expire_time = time() + $conf->get('COOKIE_LIFETIME')*86400; //Jour en secondes
		
		//On crée un cookie dans la bd uniquement si aucun autre cookie n'existe sinon
		//On rend inutilisable le cookie utilisé par un autre navigateur
		//On ne veut que cela soit le cas uniquement si on clique sur déconnexion (et que l'on a demandé Se souvenir de moi)
		$actual_cookie = $user->getCookie();
		if ($actual_cookie == "")
		{
		$cookie_token = sha1(time().rand(0,1000));
		$user->setCookie($cookie_token);
		$user->save();
		}
		else
		{
			$cookie_token = $actual_cookie;
		}	
		Functions::makeCookie($conf->get('COOKIE_NAME'),$cookie_token,$expire_time);
	}
	}
	
	header('location: ./index.php'.$error);	
	break;

	case 'GET_TOKEN':
		$user = $userManager->load(array('login'=>$_['login'],'password'=>sha1(md5($_['password']))));
		$response['token'] = $user->getToken();
		echo json_encode($response);
	break;

	
	
	case 'user_add_user':
	$right_toverify = isset($_['id']) ? 'u' : 'c';
	if($myUser->can('user',$right_toverify)){
	$user = new User();
	//Si modification on charge la ligne au lieu de la créer
	if ($right_toverify == "u"){$user = $user->load(array("id"=>$_['id']));}
	$user->setMail($_['mailUser']);
	$user->setName($_['nameUser']);
	$user->setFirstName($_['firstNameUser']);
	$user->setPassword($_['passwordUser']);
	$user->setLogin($_['loginUser']);
	$user->setRank($_['rankUser']);
	$user->setState(1);
	$user->setToken(sha1(time().rand(0,1000)));
	$user->save();
	Functions::goback("setting","user");
}
else
{
	Functions::goback("setting","user","&error=Vous n'avez pas le droit de faire ça!");
}
	break;

	case 'delete_user':
	if(!$myUser->can('user','d')) exit('ERREUR: Permissions insuffisantes.');
	$userManager = new User();
	$NbUsers = $userManager->rowCount();

	if(isset($_['id']) && $NbUsers > 1){
		$userManager->delete(array('id'=>$_['id']));
		Functions::goback("setting","user");
	}
	else
	{
		Functions::goback("setting","user","&error=Impossible de supprimer le dernier utilisateur.");
	}
	break;


	case 'access_delete_rank':
	if(!$myUser->can('configuration','d')) exit('ERREUR: Permissions insuffisantes.');
	$rankManager = new Rank();
	
	$Nbrank = $rankManager->rowCount();

	if(isset($_['id']) && $Nbrank > 1){
		$rankManager->delete(array('id'=>$_['id']));
		Functions::goback("setting","access");
		header('location:setting.php?section=access');
	}
	else
	{
		Functions::goback("setting","access","&error=Impossible de supprimer le dernier rang.");
	}
	break;

	case 'access_add_rank':
	$right_toverify = isset($_['id']) ? 'u' : 'c';
	if(!$myUser->can('configuration',$right_toverify)) exit('ERREUR: Permissions insuffisantes.');
	$rank = new Rank();
	if ($right_toverify == "u"){$rank = $rank->load(array("id"=>$_['id']));}
	$rank->setLabel($_['labelRank']);
	$rank->setDescription($_['descriptionRank']);
	$rank->save();
	Functions::goback("setting","access");
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
	Functions::goback("setting","right","&id=".$_['rankRight']);
	break;

	case 'logout':
	global $conf;
	
	//Détruire le cookie uniquement s'il existe sur cette ordinateur
	//Afin de le garder dans la BD pour les autres ordinateurs/navigateurs
	if(isset($_COOKIE[$conf->get('COOKIE_NAME')])){
	$user = new User();
	$user = $userManager->load(array("id"=>$myUser->getId()));
	$user->setCookie("");
	$user->save();
	Functions::destroyCookie($conf->get('COOKIE_NAME'));
	}

	$_SESSION = array();
	session_unset();
	session_destroy();
	


	Functions::goback(" ./index");
	break;

	

	case 'ENABLE_DASHBOARD':
		Plugin::enabled('dashboard-dashboard');
		Plugin::enabled('dashboard-monitoring-dashboard-monitoring');
		header('location: index.php');
	break;

	case 'changePluginState':
	if($myUser==false) exit('Vous devez vous connecter pour cette action.');
	if(!$myUser->can('plugin','u')) exit('ERREUR: Permissions insuffisantes.');
	if($_['state']=='0'){
		Plugin::enabled($_['plugin']);
	}else{
		Plugin::disabled($_['plugin']);
	}
	Functions::goback("setting","plugin","&block=".$_['block']);
	break;

	case 'crontab':
		Plugin::callHook("cron", array());
	break;
	
	default:
		Plugin::callHook("action_post_case", array());
	break;


	case 'GET_SPEECH_COMMAND':
	if($myUser->getId()=='') exit('{"error":"invalid or missing token"}');
	if(!$myUser->can('vocal','r')) exit('{"error":"insufficient permissions for this account"}');
	
	list($host,$port) = explode(':',$_SERVER['HTTP_HOST']);
	$actionUrl = 'http://'.$host.':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
	$actionUrl = substr($actionUrl,0,strpos($actionUrl , '?'));
	
	Plugin::callHook("vocal_command", array(&$response,$actionUrl));

	$json = json_encode($response);
	echo ($json=='[]'?'{}':$json);
	break;


	case 'GET_EVENT':
	if($myUser->getId()=='') exit('{"error":"invalid or missing token"}');
	if(!$myUser->can('vocal','r')) exit('{"error":"insufficient permissions for this account"}');
	$response = array('responses'=>array());
	Plugin::callHook("get_event", array(&$response));

	$checker = (isset($_['checker'])?$_['checker']:'client');

	$eventManager = new Event();
	$events = $eventManager->loadAll(array(),'id');
	

	$time = date('i-H-d-m-Y');
	list($minut,$hour,$day,$month,$year) = explode('-',$time);

	foreach ($events as $event) {

		if(in_array($checker,$event->getRecipients()) && $event->getState()=='1'){
			if( 
			($event->getMinut() == '*' || in_array($minut,explode(',',$event->getMinut())) ) &&
			($event->getHour() == '*' || in_array($hour,explode(',',$event->getHour())) ) &&
			($event->getDay()== '*' || in_array($day,explode(',',$event->getDay())) ) &&
			($event->getMonth() == '*' || in_array($month,explode(',',$event->getMonth())) ) &&
			($event->getYear() == '*' || in_array($year,explode(',',$event->getYear())) ) 
			){
				
				if($event->getRepeat()!=$time){
					if(in_array($checker, $event->getRecipients())){
						$event->setRepeat($time);
						$response['responses'][]= $event->getContent();

						//Le serveur ne peux qu'executer des commandes programme
						if($checker=='server'){
							$content = $event->getContent();
							switch($content['type']){
								case 'command':
									exec($content['program']);
								break;
						
								case 'gpio':
									foreach(explode(',',$content['gpios']) as $info){
										list($gpio,$state) = explode(':',$info);
										exec('gpio mode '.$gpio.' out');
										exec('gpio write '.$gpio.' '.$state);

									}
								break;
								
							}
						}

						$event->save();
					}
				}
			}
		}




	}
	
		

	$json = json_encode($response);
	echo ($json=='[]'?'{}':$json);
	break;



	case 'installPlugin':
	if($myUser==false) exit('Vous devez vous connecter pour cette action.');
	$tempZipName = 'plugins/'.md5(microtime());
	echo '<br/>Téléchargement du plugin...';
	file_put_contents($tempZipName,file_get_contents(urldecode($_['zip'])));
	if(file_exists($tempZipName)){
		echo '<br/>Plugin téléchargé <span class="label label-success">OK</span>';
		echo '<br/>Extraction du plugin...';
		$zip = new ZipArchive;
		$res = $zip->open($tempZipName);
		if ($res === TRUE) {
			$tempZipFolder = $tempZipName.'_';
			$zip->extractTo($tempZipFolder);
			$zip->close();
			echo '<br/>Plugin extrait <span class="label label-success">OK</span>';
			$pluginName = glob($tempZipFolder.'/*.plugin*.php');
			if(count($pluginName)>0){
			$pluginName = str_replace(array($tempZipFolder.'/','.enabled','.disabled','.plugin','.php'),'',$pluginName[0]);
				if(!file_exists('plugins/'.$pluginName)){
					echo '<br/>Renommage...';
					if(rename($tempZipFolder,'plugins/'.$pluginName)){
						echo '<br/>Plugin installé, <span class="label label-info">pensez à l\'activer</span>';
					}else{
						Functions::rmFullDir($tempZipFolder);
						echo '<br/>Impossible de renommer le plugin <span class="label label-error">Erreur</span>';
					}
				}else{
					echo '<br/>Plugin déjà installé <span class="label label-info">OK</span>';
				}
			}else{
				echo '<br/>Plugin invalide, fichier principal manquant <span class="label label-error">Erreur</span>';
			}

		} else {
		  echo '<br/>Echec de l\'extraction <span class="label label-error">Erreur</span>';
		}
		 unlink($tempZipName);
		}else{
			echo '<br/>Echec du téléchargement <span class="label label-error">Erreur</span>';
		}
	break;

	case 'CHANGE_GPIO_STATE':
		if($myUser==false) exit('Vous devez vous connecter pour cette action.');
	break;

	// Gestion des interfaces de seconde génération
	case 'SUBSCRIBE_TO_CLIENT':
		Action::write(function($_,&$response){
			global $myUser,$conf;
			if(!isset($_['ip'])) throw new Exception("IP invalide");
			if(!isset($_['port']) || !is_numeric($_['port'])) throw new Exception("Port invalide");
			
			$url = Functions::getBaseUrl('action.php').'/action.php';
			$client = new CLient($_['ip'],$_['port']);

			Plugin::callHook("vocal_command", array(&$vocal,$url));
			$conf =  array(
				'VOCAL_ENTITY_NAME' => $conf->put('VOCAL_ENTITY_NAME','YANA'),
				'SPEECH_COMMAND' => $vocal
			);

			if(!$client->suscribe($url,$myUser->getToken()))  throw new Exception("Appairage impossible");
			if(!$client->configure($conf))  throw new Exception("Configuration impossible");
		},array('user'=>'u'));
	break;


	default:
		Plugin::callHook("action_post_case", array());
		return Gpio::write($_['pin'],$_['state'],true);
	break;
}


?>
