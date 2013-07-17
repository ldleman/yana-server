<?php
/*
@name Plugin
@author Valentin CARRUESCO <valentin.carruesco@sys1.fr>
@link http://www.sys1.fr
@licence Copyright Sys1
@version 1.0.0
@type component
@description Module de gestion des plugins du programme
*/



function plugin_plugin_menu(){
	global $_;
	echo '<li '.(isset($_['section']) && $_['section']=='plugin'?'class="active"':'').'><a href="index.php?module=setting&section=plugin"><i class="icon-chevron-right"></i> Plugins</a></li>';
}


function plugin_plugin_page(){
	global $myUser,$_;
	if(isset($_['section']) && $_['section']=='plugin' ){

		if($myUser!=false){
		//Récuperation des plugins  
		$plugins = Plugin::getAll();
	?>

		<div class="span9 pluginBloc">
			<h1>Plugins</h1>

			<p>Voici la liste des plugins installés :</p>
			                   
			                    <ul class="pluginList">
			                   <?php if(count($plugins)==0){ ?>
			                    Aucun plugin n'est installé pour le moment.
			                   <?php }else{
			                   foreach($plugins as $plugin){
			                   		if($plugin->getType()!='component'){
			                    ?>
			                    <li>
			                        <ul>
			                            <li><h4>Nom: </h4><?php echo $plugin->getName(); ?></li>
			                            <li><h4>Auteur: </h4><a href="mailto:{$value->getMail(); ?>"><?php echo $plugin->getAuthor(); ?></a></li>
			                            <li><h4>Licence: </h4><?php echo $plugin->getLicence(); ?></li>
			                            <li><h4>Version: </h4><code><?php echo $plugin->getVersion(); ?></code></li>
			                            <li><h4>Site web: </h4><a href="<?php echo $plugin->getLink(); ?>"><?php echo $plugin->getLink(); ?></a></li>
			                            <li><?php echo $plugin->getDescription(); ?></li>
			                            <li><a href="action.php?action=changePluginState&plugin=<?php echo $plugin->getUid(); ?>&state=<?php echo $plugin->getState(); ?>" class="button"><?php echo ($plugin->getState()=="0"?"Activer":"Désactiver") ?></a></li>
			                        </ul>
			                    </li>
			                  
			                    <?php }}} ?>
			                    </ul>
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


Plugin::addHook("setting_menu", "plugin_plugin_menu");  
Plugin::addHook("setting_bloc", "plugin_plugin_page"); 
?>