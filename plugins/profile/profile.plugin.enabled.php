<?php
/*
@name Profile
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@description affichage du profil et des infos
*/


function profil_plugin_actions(){
	global $myUser,$_,$conf;
	switch($_['action']){
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
	echo '<li '.((isset($_['section']) && $_['section']=='profil') || !isset($_['section']) ?'class="active"':'').'><a href="setting.php?section=profil"><i class="icon-chevron-right"></i> Profil</a></li>';
}

function profil_plugin_page(){
	global $myUser,$_,$conf;
	if((isset($_['section']) && $_['section']=='profil') || !isset($_['section'])  ){
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


Plugin::addHook("setting_menu", "profil_plugin_menu");  
Plugin::addHook("setting_bloc", "profil_plugin_page"); 
Plugin::addHook("action_post_case", "profil_plugin_actions");

?>