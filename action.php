<?php

require_once __DIR__.DIRECTORY_SEPARATOR."common.php";


if(!isset($_['action'])) throw new Exception('Action inexistante');

//Execution du code en fonction de l'action
switch ($_['action']){
	
	
	case 'login':
		global $myUser;
		try{
			
			$myUser = User::check($_['login'],$_['password']);
			if(!$myUser->connected()) throw new Exception('Utilisateur inexistant');
			$myUser->loadRights();
		
			$_SESSION['currentUser'] = serialize($myUser);
			
		}catch(Exception $e){
		
			header('location: index.php?error='.urlencode($e->getMessage()));
		}
		header('location: index.php?error=');
	break;
	
	case 'logout':
		unset($_SESSION['currentUser']);
		session_destroy();
		header('location: index.php');
	break;

	//WIDGET
	
	case 'refresh_widget':
		Action::write(function($_,&$response){
			global $myUser;
			if(!$myUser->can('widget','read')) throw new Exception("Permissions insuffisantes");
			$widgets = array();
			Plugin::callHook('widget_refresh',array(&$widgets));
			$response['rows'] = $widgets;
		});
	break;
	
	case 'search_widget':

		Action::write(function($_,&$response){
			global $myUser;
			if(!$myUser->can('widget','read')) throw new Exception("Permissions insuffisantes");
			
			$models = array();
			Plugin::callHook('widget',array(&$models));
			foreach($models as $model):
				$models[$model->model] = $model;
			endforeach;
			
			$widgets = Widget::loadAll(array('dashboard'=>$_['dashboard']));
			
			foreach($widgets as $widget):
				if(!isset($models[$widget->model])) continue;

				$model = $models[$widget->model];
				$model->id = $widget->id;
				$model->position = $widget->position;
				$model->minified = $widget->minified;
				$model->dashboard = $widget->dashboard;
				$response['rows'][] = $model;
			endforeach;
		});


	break;

	//ROOM
	case 'search_room':
		Action::write(function($_,&$response){
			global $myUser;
			if(!$myUser->can('room','read')) throw new Exception("Permissions insuffisantes");
			foreach(Room::loadAll()as $room){
				$response['rows'][] = $room;
			}
				
		});
	break;
	
	case 'save_room':
		Action::write(function($_,&$response){
			global $myUser;
			if(!$myUser->can('room','edit')) throw new Exception("Permissions insuffisantes");
			$room = isset($_['id']) ? Room::getById($_['id']) : new Room();
			if(!isset($_['label']) || empty($_['label'])) throw new Exception("Nom obligatoire");
			$room->label = $_['label'];
			$room->description = $_['description'];
			$room->save();
		});
	break;
	
	case 'edit_room':
		Action::write(function($_,&$response){
			global $myUser;
			if(!$myUser->can('room','edit')) throw new Exception("Permissions insuffisantes");
			$room = Room::getById($_['id']);
			$response = $room;
		});
	break;

	case 'delete_room':
		Action::write(function($_,&$response){
			global $myUser;
			if(!$myUser->can('room','delete')) throw new Exception("Permissions insuffisantes");
			Room::deleteById($_['id']);
		});
	break;

	// PLUGINS
	case 'search_plugin':
		Action::write(function($_,&$response){
			global $myUser;
			if(!$myUser->can('plugin','read')) throw new Exception("Permissions insuffisantes");
			foreach(Plugin::getAll() as $plugin){
				$plugin->folder = array('name'=>$plugin->folder,'path'=>$plugin->path());
				$response['rows'][] = $plugin;
			}
				
		});
	break;
	
	case 'change_plugin_state':
		Action::write(function($_,&$response){
			global $myUser;
			if(!$myUser->can('plugin','configure')) throw new Exception("Permissions insuffisantes");
			
			$plugin = Plugin::getById($_['plugin']);
			
			if($_['state']){
				$states = Plugin::states();
				$missingRequire = array();
				foreach($plugin->require as $require=>$version):
					$req = Plugin::getById($require);
					if($req == null || $req==false || !$req->state || $req->version!=$version)
						$missingRequire[]= $require.' - '.$version;
				endforeach;
				if(count($missingRequire)!=0) throw new Exception("Plugins pré-requis non installés : ".implode(',',$missingRequire));
			}
			
			Plugin::state($_['plugin'],$_['state']);
		});
	break;
	
	
 	//USER
	case 'search_user':
		Action::write(function($_,&$response){
			global $myUser;
			if(!$myUser->can('user','read')) throw new Exception("Permissions insuffisantes");
			foreach(User::getAll()as $user){
				$user->avatar = $user->getAvatar();
				$user->rank = $user->rank_object;
				$response['rows'][] = $user;
			}
				
		});
	break;
	
	case 'save_user':
		Action::write(function($_,&$response){
			global $myUser;
			if(!$myUser->can('user','edit') && ($_['account']=="false" || $myUser->login!=$_['login']) ) throw new Exception("Permissions insuffisantes");

			if($_['password']!=$_['password2']) throw new Exception("Mot de passe et confimration non similaires");
			$user = isset($_['id']) ? User::getById($_['id']) : new User();

			if($user->id==0){
				if(!isset($_['login']) || empty($_['login'])) throw new Exception("Identifiant obligatoire");
				if(!isset($_['rank']) || empty($_['rank'])) throw new Exception("Rang obligatoire");
			}

			if(!empty(trim($_['password']))) $user->password = sha1(md5($_['password']));
			$user->name = $_['password'];
			$user->firstname = $_['password'];
			$user->mail = $_['mail'];

			file_put_contents(__ROOT__.AVATAR_PATH.$_['login'].'.jpg',getGravatar($_['mail'],150));

			if($_['account']!="true"){ 
				$user->rank = $_['rank'];
				$user->login = $_['login'];
			}

			$user->save();
		});
	break;
	
	case 'edit_user':
		Action::write(function($_,&$response){
			global $myUser;
			if(!$myUser->can('user','edit')) throw new Exception("Permissions insuffisantes");
			$user = User::getById($_['id']);
			$user->password='';
			$response = $user;
		});
	break;

	case 'delete_user':
		Action::write(function($_,&$response){
			global $myUser;
			if(!$myUser->can('user','delete')) throw new Exception("Permissions insuffisantes");
			$user = User::getById($_['id']);
			if($user->superadmin) throw new Exception("Vous ne pouvez pas supprimer le compte super admin");
			User::deleteById($_['id']);
		});
	break;

	//RIGHT
	case 'save_right':
		Action::write(function($_,&$response){
			global $myUser;
			if(!$myUser->can('rank','edit')) throw new Exception("Permissions insuffisantes");
			if(!isset($_['section']) || empty($_['section'])) throw new Exception("Droit non spécifié");
			if(!isset($_['rank']) || empty($_['rank'])) throw new Exception("Rang non spécifié");
			if(!isset($_['right']) || empty($_['right'])) throw new Exception("Droit non spécifié");
			
			$item = Right::load(array('rank'=>$_['rank'],'section'=>$_['section']));
			$item = !$item ? new Right(): $item ;
			$item->rank = $_['rank'];
			$item->section = $_['section'];
			$item->{$_['right']} = $_['state']=='true';
			$item->save();
			
			$myUser->loadRights();
			$_SESSION['currentUser'] = serialize($myUser);
			
		});
	break;
	

	//RANGS
	case 'search_rank':
		Action::write(function($_,&$response){
			global $myUser;
			if(!$myUser->can('rank','read')) throw new Exception("Permissions insuffisantes");
			foreach(Rank::loadAll()as $user){
				$response['rows'][] = $user;
			}
		});
	break;
	
	case 'save_rank':
		Action::write(function($_,&$response){
			global $myUser;
			if(!$myUser->can('rank','edit')) throw new Exception("Permissions insuffisantes");
			if(!isset($_['label']) || empty($_['label'])) throw new Exception("Libellé obligatoire");
			$item = isset($_['id']) ? Rank::getById($_['id']) : new Rank();
			$item->label = $_['label'];
			$item->description = $_['description'];
			$item->save();
		});
	break;
	
	case 'edit_rank':
		Action::write(function($_,&$response){
			global $myUser;
			if(!$myUser->can('rank','edit')) throw new Exception("Permissions insuffisantes");
			$response = Rank::getById($_['id']);
		});
	break;

	case 'delete_rank':
		Action::write(function($_,&$response){
			global $myUser;
			if(!$myUser->can('rank','delete')) throw new Exception("Permissions insuffisantes");
			Rank::deleteById($_['id']);
		});
	break;
	
	/*LIST*/
	
	case 'fill_list_table':
		Action::write(function($_,&$response){
			global $myUser;
			if(!is_numeric($_['id'])) throw new Exception("List ID non spécifié");
			$dic = new Dictionnary();
			$dic = $dic->getById($_['id']);
			$response['dictionnary']  = $dic->toArray();
			foreach (Dictionnary::childs( $dic->slug) as $child) {
				$child->label = stripcslashes(html_entity_decode($child->label, ENT_QUOTES));
				$response['rows'][] = $child;
			}
		});
	break;
	case 'save_list_table':
		Action::write(function($_,&$response){
			global $myUser;
			if(!isset($_['id']) && !isset($_['list'])) throw new Exception("List/ITEM ID non spécifié");
			if(empty($_['label'])) throw new Exception("Valeur vide");
			
			$dic = new Dictionnary();
			$dic = isset($_['id']) && !empty($_['id'])? $dic->getById($_['id']) : new Dictionnary();
			if(isset($_['list']))$dic->parent = $_['list'];
			$dic->label = $_['label'];
			$dic->save();
		});
	break;

	case 'edit_list_table':
		Action::write(function($_,&$response){
			global $myUser;
			if(!is_numeric($_['id'])) throw new Exception("List ID non spécifié");
			$dic = new Dictionnary();
			$dic = $dic->getById($_['id']);
			$dic->label = str_replace("&#039;","'",$dic->label);
			$response['item'] = $dic->toArray();
		});
	break;

	case 'delete_list_table':
		Action::write(function($_,&$response){
			global $myUser;
			if(!is_numeric($_['id'])) throw new Exception("List ID non spécifié");
			$dic = new Dictionnary();
			$dic->delete(array('id'=>$_['id']));

		});
	break;
	
	default:
		Plugin::callHook('action');
	break;
}


?>