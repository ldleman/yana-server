<?php
/*
@name Home
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@type component
@description Permet l'affichage d'une page d'accueil par défaut
*/


function home_plugin_menu(&$menuItems){
	$menuItems[] = array('sort'=>0,'content'=>'<a href="index.php"><i class="icon-home"></i> Accueil</a>');
}


function home_plugin_page($_){
	if(!isset($_['module']) || $_['module']=='home'){
	?>


	<div class="span9">

	<h1>Accueil</h1>
	<p>Bienvenue !</p>

	</div>
<?php
	}
}

//Plugin::addCss("/css/style.css"); 
//Plugin::addJs("/js/main.js"); 

Plugin::addHook("menubar_pre_home", "home_plugin_menu");  
Plugin::addHook("home", "home_plugin_page");  
?>