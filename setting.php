<?php
require_once('header.php');


switch(@$_['section']){
	case 'plugin':
		$plugins = Plugin::getAll();
		$tpl->assign('plugins',$plugins);
	break;

	case 'user':
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
require_once('footer.php');
?>