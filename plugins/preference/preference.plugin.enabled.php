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
	       <?php Plugin::callHook("preference_menu", array()); ?>
	    </ul>

		 <?php Plugin::callHook("preference_content", array()); ?>
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


Plugin::addHook("setting_menu", "preference_plugin_menu");  
Plugin::addHook("setting_bloc", "preference_plugin_page"); 
?>