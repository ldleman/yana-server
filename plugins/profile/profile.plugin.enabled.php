<?php
/*
@name Profile
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@description affichage du profil et des infos
*/


function dash_profil_plugin_menu(&$widgets){
		$widgets[] = array(
		    'uid'      => 'dash_profil',
		    'icon'     => 'fa fa-globe',
		    'label'    => 'Connecté',
		    'background' => '#1BA1E2', 
		    'color' => '#fffffff',
		    'onLoad'   => 'action.php?action=dash_profil_plugin_load',
		    'onMove'   => 'action.php?action=dash_profil_plugin_move',
		    //'onSave'   => 'action.php?action=dash_profil_plugin_save',
		    'onEdit'   => 'action.php?action=dash_profi_plugin_edit',
		    'onDelete' => 'action.php?action=dash_profil_plugin_delete'
		    );

}

function profil_plugin_actions(){
	global $myUser,$_,$conf;
	switch($_['action']){
		case 'dash_profi_plugin_edit':
			echo 'L\'edition de ce bloc est disponible depuis <a href="setting.php?section=profil">la page d\'edition du profil</a>';
		break;
		case 'dash_profil_plugin_load':
		header('content-type:application/json');
		$response['title'] = 'Connecté';

		$url_link = Functions::getBaseUrl('action.php').'/action.php';
		
		$response['content'] = '<div id="dash_application">'.$myUser->getGravatarImg().'
			    <ul class="user-infos">
			    	<li><h1 onclick="window.location=\'setting.php?section=profil\';"><i class="fa fa-pencil"></i>'.$myUser->getFullName().'</h1></li>
			    	<li><a href="mailto:'.$myUser->getMail().'">'.$myUser->getMail().'</a></li>
			    	<li><div class="tokenbox" title="'.$myUser->getToken().'">Token : <input type="text" onclick="$(this).select();" value="'.$myUser->getToken().'"></div></li>
			    </ul>
				<a href="#yanaWindowsModal" role="button" data-toggle="modal"  class="btn btn-primary"><i class="fa fa-download-alt  fa fa-white"></i> Installer YANA Windows</a></div>


				<!-- Modal -->
				<div id="yanaWindowsModal" class="modal hide fade" style="width:750px;margin-left:-375px;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				  <div class="modal-header">
				    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				    <h3 id="myModalLabel">Installer/Lier à yana windows</h3>
				  </div>
				  <div class="modal-body" >
				    <p>Pour avoir accès à la partie vocale, vous devez installer yana-windows. Pour cela suivez les inscructions ci dessous.</p>
					<ul>
				    <li>Si ce n\'est pas déja fait <a href="https://github.com/ldleman/yana-windows/archive/master.zip">téléchargez Yana Windows</a> et décompressez le.</li>
				  <li>Exécutez le programme <b>"ScanSoft Virginie_Dri40_16kHz.exe"</b> pour installez la voix de yana</li>
				  <li>Lancez le programme <b>"yana.exe"</b>, puis faites un clic droit sur l\'îcone de yana situé dans la barre de tâche et cliquez sur \'Configuration\'
				  <li>Configurez \'Adresse du serveur\' avec la valeur suivante : <code>'.$url_link.'</code></li> 
				  <li>Dans le champs suivant, entrez le \'Token\' d\'identification suivant : <code>'.$myUser->getToken().'</code></li> 
				  <li>Cliquez sur enregistrer, le programme se relance et l\'installation est terminée !!</li> 
				</ul>
				  </div>
				  <div class="modal-footer">
				    <button class="btn" data-dismiss="modal" aria-hidden="true">Fermer</button>
				  </div>
				</div>

				';
				echo json_encode($response);
		break;
		case 'profile_set_profile':
			$myUser->setFirstName($_['firstname']);
			$myUser->setName($_['name']);
			$myUser->setLogin($_['login']);
			$myUser->setMail($_['mail']);

			$fields = array('mail'=>$myUser->getMail(),'login'=>$myUser->getLogin(),'firstname'=>$myUser->getFirstName(),'name'=>$myUser->getName());

			if(trim($_['password']) !=''){
				$fields['password'] = User::cryptPassword($_['password']);
			}
			
			$userManager = new User();
			$userManager->change($fields,array('id'=>$myUser->getId()));
			$_SESSION['currentUser'] = serialize($myUser);
			header('location: setting.php?section=profil');
		break;
	}
}


function profil_plugin_menu(){
	global $_;
	echo '<li '.((isset($_['section']) && $_['section']=='profil') ?'class="active"':'').'><a href="setting.php?section=profil"><i class="fa fa-angle-right"></i> Profil</a></li>';
}

function profil_plugin_page(){
	global $myUser,$_,$conf;
	if((isset($_['section']) && $_['section']=='profil')  ){
		if($myUser!=false){
	?>

		<div class="span9 userBloc">
		<h1>Mon profil</h1>
		<p>Page de profil de <?php echo $myUser->getFullName(); ?></p>
	    <form class="left" action="action.php?action=profile_set_profile" style="margin-right:15px;" method="POST">
	    <label for="name">Nom</label>
	    	<input type="text" class="input-large" id="name" name="name" value="<?php echo $myUser->getName(); ?>"><br/>
	    <label for="firstname">Prénom</label>
	    	<input type="text" class="input-large" id="firstname" name="firstname" value="<?php echo $myUser->getFirstName(); ?>"><br/>
	    <label for="login">Identifiant</label>
	    	<input type="text" class="input-large" id="login" name="login" value="<?php echo $myUser->getLogin(); ?>"><br/>
	    <label for="mail">Email</label>
	    	<input type="text" class="input-large" id="mail" name="mail" value="<?php echo $myUser->getMail(); ?>"><br/>
	    <label for="password">Mot de passe</label>
	    	<input type="password" class="input-large" id="password" name="password" value=""><br/>

	    <button type="submit" class="btn">Modifier</button><br/>
		<br/>
	    
	    </form>
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

Plugin::addCss('/css/style.css',true);
Plugin::addHook("setting_menu", "profil_plugin_menu");  
Plugin::addHook("setting_bloc", "profil_plugin_page"); 
Plugin::addHook("action_post_case", "profil_plugin_actions");
Plugin::addHook("widgets", "dash_profil_plugin_menu");
?>