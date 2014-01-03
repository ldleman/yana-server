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
	

	if(isset($_['rememberMe'])){	
		$expire_time = time() + COOKIE_LIFETIME*86400; //Jour en secondes
		
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
		Functions::makeCookie(COOKIE_NAME,$cookie_token,$expire_time);
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

	//Détruire le cookie uniquement s'il existe sur cette ordinateur
	//Afin de le garder dans la BD pour les autres ordinateurs/navigateurs
	if(isset($_COOKIE[COOKIE_NAME])){
	$user = new User();
	$user = $userManager->load(array("id"=>$myUser->getId()));
	$user->setCookie("");
	$user->save();
	Functions::destroyCookie(COOKIE_NAME);
	}

	$_SESSION = array();
	session_unset();
	session_destroy();
	


	Functions::goback(" ./index");
	break;


	case 'changePluginState':
	if($myUser==false) exit('Vous devez vous connecter pour cette action.');
	if(!$myUser->can('plugin','u')) exit('ERREUR: Permissions insuffisantes.');
	if($_['state']=='0'){
		Plugin::enabled($_['plugin']);

	}else{
		Plugin::disabled($_['plugin']);
	}
	Functions::goback("setting","plugin");
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
	$actionUrl = 'http://'.$_SERVER['SERVER_ADDR'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
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

		if(in_array($checker,$event->getRecipients())){
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


	case 'GET_DASH_INFO':
		switch($_['type']){

			/*$tpl->assign('users',Monitoring::users());
		$tpl->assign('hdds',Monitoring::hdd());
		$tpl->assign('services',Monitoring::services());
		$tpl->assign('ethernet',Monitoring::ethernet());
		$tpl->assign('ram',Monitoring::ram());
		$tpl->assign('cpu',Monitoring::cpu());
		$tpl->assign('heat',Monitoring::heat());
		$tpl->assign('disks',Monitoring::disks());*/
			case 'dash_system':
				//$heat = Monitoring::heat();
				$heat = shell_exec("/opt/vc/bin/vcgencmd measure_temp | cut -c 6-");
				$cpu = Monitoring::cpu();
				echo '<ul>
				    	<li><strong>Distribution :</strong> '.Monitoring::distribution().'</li>
				    	<li><strong>Kernel :</strong> '.Monitoring::kernel().'</li>
				    	<li><strong>HostName :</strong> '.Monitoring::hostname().'</li>
				    	<li><strong>Temperature :</strong>  <span class="label label-warning">'.$heat.'</span></li>
				    	<li><strong>Temps de marche :</strong> '.Monitoring::uptime().'</li>
				    	<li><strong>CPU :</strong>  <span class="label label-info">'.$cpu['current_frequency'].' Mhz</span> (Max '.$cpu['maximum_frequency'].'  Mhz/ Min '.$cpu['minimum_frequency'].'  Mhz)</li>
				    </ul>';
					/* <li><strong>Temperature :</strong> <span class="label label-warning">'.$heat['degree'].'</span></li>  // Au cas ou 
					   <li><strong>Temperature RaspCtrl :</strong> '.Monitoring::heat().'</li>*/
			break;
			case 'dash_network':
			$ethernet = Monitoring::ethernet();
			echo '<ul>
			    	<li><strong>IP LAN :</strong> <code>'.Monitoring::internalIp().'</code></li>
			    	<li><strong>IP WAN :</strong> <code>'.Monitoring::externalIp().'</code></li>
			    	<li><strong>Serveur HTTP :</strong> '.Monitoring::webServer().'</li>
			    	<li><strong>Ethernet :</strong> '.$ethernet['up'].' Montant / '.$ethernet['down'].' Descendant</li>
			    	<li><strong>Connexions :</strong>  <span class="label label-info">'.Monitoring::connections().'</span></li>
			    </ul>';
			break;
			case 'dash_user':
				echo '<ul>';
				$users = Monitoring::users();
			    foreach ($users as $value) {
					echo '<li>Utilisateur <strong class="badge">'.$value['user'].'</strong> IP : <code>'.$value['ip'].'</code>, Connexion : '.$value['hour'].' </li>';
			    }
			    echo '</ul>';
			break;
			case 'dash_hdd':
				$hdds = Monitoring::hdd();
				echo '<ul>';

				foreach ($hdds as $value) {
					'<li><strong class="badge">'.$value['name'].'</strong> Espace : '.$value['used'].'/'.$value['total'].' Format : '.$value['format'].' </li>';
				}
				echo '</ul>';
			break;
			case 'dash_disk':
				$disks = Monitoring::disks();
				echo '<ul>';
			    foreach ($disks as $value) {
			    	echo '<li><strong class="badge">'.$value['name'].'</strong> Statut : '.$value['size'].' Type : '.$value['type'].' Chemin : '.$value['mountpoint'].'  </li>';
			    }
			    echo '</ul>';
			break;
			case 'dash_services':
				$services = Monitoring::services();
				echo '<ul>';
			    foreach ($services as $value) {
			    	echo '<li '.($value['status']?'class="service-active"':'').'>- '.$value['name'].'</li>';
			    }
			    echo '</ul>';
			break;
			case 'dash_gpio':
				$gpios = Monitoring::gpio();
				$pin=array("GPIO 0","GPIO 1","GPIO 2","GPIO 3","GPIO 4","GPIO 5","GPIO 6","GPIO 7","   SDA","SCL   ","   CE0","CE1   ","  MOSI","MOSO  ","  SCLK","TxD   ","   RxD","GPIO 8","GPIO 9","GPIO10","GPIO11","JOKER!");
				echo '<pre><ul>';
			    for ($i = 0; $i <= 21; $i+=2) {
			    	echo '     <strong>'.$pin[$i].'</strong>-> '.($gpios[$i]?'<span class="label label-warning">on&nbsp</span>':'<span class="label label-info">off</span>').'  '.($gpios[($i+1)]?'<span class="label label-warning">on&nbsp</span>':'<span class="label label-info">off</span>').' <-<strong>'.$pin[($i+1)].'</strong><br/>';
			    }

			    echo '</ul></pre>';
			break;
		}
	break;

	default:
	Plugin::callHook("action_post_case", array());
	break;
}


?>
