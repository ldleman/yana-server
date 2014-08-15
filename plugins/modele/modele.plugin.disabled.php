<?php
/*
@name Modele
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@description Modele de plugin pour les modules
*/

//Si vous utiliser la base de donnees a ajouter
include('Modele.class.php');

//Cette fonction va generer un nouveau element dans le menu
function test_plugin_menu(&$menuItems){
	global $_;
	$menuItems[] = array('sort'=>10,'content'=>'<a href="index.php?module=test"><i class="fa fa-codepen"></i> Modele</a>');
}

//Cette fonction ajoute une commande vocale
function test_plugin_vocal_command(&$response,$actionUrl){
	global $conf;
	//Création de la commande vocale "Yana, commande de test" avec une sensibilité de 0.90 et un appel 
	// vers l'url /action.php?action=test_plugin_vocal_test après compréhension de la commande
	$response['commands'][] = array(
		'command'=>$conf->get('VOCAL_ENTITY_NAME').' commande vocale de test',
		'url'=>$actionUrl.'?action=test_plugin_vocal_test','confidence'=>('0.90'+$conf->get('VOCAL_SENSITIVITY'))
		);
}

//cette fonction comprends toutes les actions du plugin qui ne nécessitent pas de vue html
function test_plugin_action(){
	global $_,$conf;

	//Action de réponse à la commande vocale "Yana, commande de test"
	switch($_['action']){
		case 'test_plugin_vocal_test':
			$response = array('responses'=>array(
										array('type'=>'talk','sentence'=>'Ma réponse à la commande de test est inutile.')
											)
								);
			$json = json_encode($response);
			echo ($json=='[]'?'{}':$json);
		break;
	}
}


//Cette fonction va generer une page quand on clique sur Modele dans menu
function test_plugin_page($_){
	if(isset($_['module']) && $_['module']=='test'){
	?>
	<div class="span3 bs-docs-sidebar">
	  <ul class="nav nav-tabs nav-stacked">
	    <li  class="active"><a href="#components"><i class="fa fa-angle-right"></i> 1. Menu 1</a></li>
	    <li><a href=""><i class="fa fa-angle-right"></i> 2. Menu 2</a></li>
	    <li><a href=""><i class="fa fa-angle-right"></i> 3. Menu 3</a></li>
	    <li><a href=""><i class="fa fa-angle-right"></i> 4. Menu <span class="badge badge-warning">4</span></a></li>
	  </ul>
	</div>

	<div class="span9">


	<h1>Titre du module</h1>
	<p>Description courte et/ou message d'introduction</p>


		<h2>Bloc d'onglets</h2>

	    <ul class="nav nav-tabs">
	      <li class="active"><a href="#">Onglet 1</a></li>
	      <li><a href="#">Onglet 2</a></li>
	      <li><a href="#">Onglet 2</a></li>
	    </ul>

	    <h2>Tableau</h2>
	    <table class="table table-striped table-bordered table-hover">
	    <thead>
	    <tr>
	    <th>Title</th>
	    <th>Title</th>
	    </tr>
	    </thead>
	    <tr><td>col1</td><td>col2</td></tr>
	    <tr><td>col3</td><td>col4</td></tr>
	    </table>

	    <h2>Barre de progression</h2>
	    <div class="progress progress-striped active">
	    <div class="bar" style="width: 40%;"></div>
	    </div>

	    <h2>Pagination</h2>
	    <div class="pagination">
	    <ul>
	    <li><a href="#">Prev</a></li>
	    <li><a href="#">1</a></li>
	    <li><a href="#">2</a></li>
	    <li><a href="#">3</a></li>
	    <li><a href="#">4</a></li>
	    <li><a href="#">5</a></li>
	    <li><a href="#">Next</a></li>
	    </ul>
	    </div>

	</div>
<?php
	}
}

Plugin::addCss("/css/style.css"); 
//Plugin::addJs("/js/main.js"); 

Plugin::addHook("menubar_pre_home", "test_plugin_menu");  
Plugin::addHook("home", "test_plugin_page");  
Plugin::addHook("action_post_case", "test_plugin_action");    
Plugin::addHook("vocal_command", "test_plugin_vocal_command");
?>