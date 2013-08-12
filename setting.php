<?php
require_once('header.php');

if(isset($myUser) && $myUser!=false){

switch(@$_['section']){
	case 'plugin':
		$plugins = Plugin::getAll();
		$tpl->assign('plugins',$plugins);
	break;

	case 'user':

	//Gestion de la modification des utilisateurs
		if (isset($_['id_user'])){
			$usersManager = new User();
			$id_mod = $_['id_user'];
			$selected = $usersManager->getById($id_mod);
			$addormodify_text = $selected->GetFirstName()." ".$selected->GetName();
			$action = "action.php?action=user_mod_user&id_user=".$id_mod;
			$addormodify_buttontext = "Modifier";
			$tpl->assign('addormodify_text',$addormodify_text);
			$tpl->assign('action',$action);
			$tpl->assign('addormodify_buttontext',$addormodify_buttontext);

			$tpl->assign('login',$selected->getLogin());
			$tpl->assign('firstname',$selected->getFirstName());
			$tpl->assign('lastname',$selected->getName());
			$tpl->assign('email',$selected->getMail());
			$tpl->assign('userrank',$selected->getRank());
		}
		else
		{
			$addormodify_text =  "Ajout d'un utilisateur";
			$action = "action.php?action=user_add_user";
			$addormodify_buttontext = "Ajouter";
			$tpl->assign('addormodify_text',$addormodify_text);
			$tpl->assign('action',$action);
			$tpl->assign('addormodify_buttontext',$addormodify_buttontext);
			$tpl->assign('userrank','');
		}


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

require_once('footer.php');
?>