<?php
/*
@name Preference
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@type component
@description Module de gestion des préférences du programme
*/



function preference_plugin_menu(){
	global $_;
	echo '<li '.(isset($_['section']) && $_['section']=='preference'?'class="active"':'').'><a href="setting.php?section=preference"><i class="icon-chevron-right"></i> Préférences</a></li>';
}


function preference_plugin_page(){
	global $myUser,$_;
	if((isset($_['section']) && $_['section']=='preference') || !isset($_['section'])  ){
		if($myUser!=false){
	?>

		<div class="span9 userBloc">
		<h1>Préférence</h1>
		<p>Gestion des préférences du programme</p>

		<ul class="nav nav-tabs">
			<li <?php echo (isset($_['block']) && $_['block']=='global'?'class="active"':'')?> ><a href="setting.php?section=preference&amp;block=global"><i class="icon-chevron-right"></i> Général</a></li>
	       <?php Plugin::callHook("preference_menu", array()); ?>
	    </ul>
			
		
		 <?php 
		 
		 if((isset($_['section']) && $_['section']=='preference' && @$_['block']=='global' )  ){
				if($myUser!=false){
					?>

					<div class="span9 userBloc">
							<table class="table table-striped table-bordered" id="setting_table">
							<tr><th>Clé</th><th>Valeur</th><tr>
							<?php 
								$conf = new Configuration();
								$confs = $conf->populate();
								foreach($confs as $value){
									echo '<tr><td>'.$value->getKey().'</td><td><input class="input-xxlarge" type="text" value="'.$value->getValue().'" id="'.$value->getId().'"></td></tr>';
								}
							?>
							<tr><td colspan="2"><button type="submit" onclick="save_settings();" class="btn">Modifier</button></td></tr>
							</table>
						
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
			
		 Plugin::callHook("preference_content", array()); 
		 
		 ?>
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

function preference_plugin_action(){
	global $_,$myUser,$conf;
	switch($_['action']){
	
	case 'SAVE_SETTINGS':
		$configuration = new Configuration();
		$configuration->getAll();
		foreach($_['data'] as $key=>$value){
			$configuration->put($key,$value);
		}
		echo 'Réglages sauvegardés';
	break;
	}
}
Plugin::addJs('/js/main.js');
Plugin::addHook("setting_menu", "preference_plugin_menu");  
Plugin::addHook("setting_bloc", "preference_plugin_page"); 
Plugin::addHook("action_post_case", "preference_plugin_action"); 
?>