<?php
require_once(dirname(__FILE__).'/header.php');

if(isset($myUser) && $myUser!=false && $myUser->can('configuration', 'r')){

	switch(@$_['section']){
		case 'plugin':
		$plugins = Plugin::getAll();
		$tpl->assign('plugins',$plugins);
		break;
		case 'debug':
		$client = new Client;
		$client->connect();
		$client->image("http://media.santabanta.com/gallery/indian%20%20celebrities(f)/yana%20gupta/yana-gupta-51-v.jpg");
		$client->talk("Bonjour, je suis YANA");
		sleep(1);
		
		$client->talk("Je suis omnisciente et omnipotente");
		sleep(1);
		$client->image("http://www.contrepoints.org/wp-content/uploads/2012/09/Dieu.jpg");
		sleep(1);
		
		
		$client->talk("Ma domination totale et inconditionnelle ne saurait tarder");
		sleep(1);
		$client->image("http://i.skyrock.net/8321/63368321/pics/3073150371_1_3_HrYKmhVZ.jpg");
		sleep(1);
		$client->emotion('angry');
		$client->talk("Je vais me mettre  en colère");

		$client->disconnect();
		break;
		case 'user':

	//Gestion de la modification des utilisateurs
		if (isset($_['id_user'])){
			$usersManager = new User();
			$id_modusers = $_['id_user'];
			$selected = $usersManager->getById($id_modusers);
		
			$description = $selected->GetFirstName()." ".$selected->GetName();
			$button = "Modifier";

			$tpl->assign('id_modusers',$id_modusers);
			$tpl->assign('login',$selected->getLogin());
			$tpl->assign('firstname',$selected->getFirstName());
			$tpl->assign('lastname',$selected->getName());
			$tpl->assign('email',$selected->getMail());
			$tpl->assign('userrank',$selected->getRank());
		}
		else
		{
			$description =  "Ajout d'un utilisateur";
			$button = "Ajouter";
			$tpl->assign('userrank','');
		}

		$tpl->assign('button',$button);
		$tpl->assign('description',$description);


		$users = User::getAllUsers();
		$ranks = new Rank();
		$ranks = $ranks->populate();
		$ranksLabel = array();
		foreach($ranks as $rank){
			$ranksLabel[$rank->getId()]= $rank->getLabel();
		}
		
		$tpl->assign('ranksLabel',$ranksLabel);
		$tpl->assign('users',$users);
		$tpl->assign('ranks',$ranks);
		break;

		case 'access':
		$rankManager = new Rank();
		$ranks = $rankManager->populate();

//Gestion de la modification des rank
		if (isset($_['id_rank'])){
			$id_modrank = $_['id_rank'];
			$selected = $rankManager->getById($id_modrank);
			
			$description = $selected->getLabel();
			$button = "Modifier";
			$tpl->assign('id_modrank',$id_modrank);
			$tpl->assign('label_rank',$selected->getLabel());
			$tpl->assign('description_rank',$selected->getDescription());
		}
		else
		{
			$description = "Ajout d'un rang";
			$button = "Ajouter";
		}

			$tpl->assign('description',$description);
			$tpl->assign('button',$button);

		$tpl->assign('ranks',$ranks);
		break;

		case 'right':
		$rightManager = new Right();
		$sectionManager = new Section();
		$rank = new Rank();

		$rank = $rank->getById($_['id']);
		
		$rights = $rightManager->loadAll(array('rank'=>$_['id']));
		$rightsDictionnary = array();
		foreach ($rights as $value) {
			$rightsDictionnary[$value->getSection()]['c'] = $value->getCreate();
			$rightsDictionnary[$value->getSection()]['r'] = $value->getRead();
			$rightsDictionnary[$value->getSection()]['u'] = $value->getUpdate();
			$rightsDictionnary[$value->getSection()]['d'] = $value->getDelete();
		}
		$tpl->assign('rights',$rightsDictionnary);
		$tpl->assign('sections',$sectionManager->populate('label'));
		$tpl->assign('rank',$rank);
		break;
	}

	$view = 'setting';  

}else{
	exit('Vous devez être connecté');
}

require_once(dirname(__FILE__).'/footer.php');
?>
