<?php


//Cette fonction va generer un nouveau element dans le menu
function test_plugin_menu(&$menuItems){
	global $_;
	$menuItems[] = array(
	'sort'=>10,
	'url'=>'index.php?module=modele',
	'label'=>'Modele',
	'icon'=>'codepen'
	);
}



//cette fonction comprends toutes les actions du plugin qui ne nécessitent pas de vue html
function test_plugin_action(){
	global $_,$conf;
	switch($_['action']){
		case 'test_plugin_action_1':
			
		break;
	}
}


//Cette fonction va generer une page quand on clique sur Modele dans menu
function test_plugin_page(){
	global $_;
	if(!isset($_['module']) || $_['module']!='modele') return;
	require_once('Voiture.class.php');
	?>
	<h3>Mon plugin</h3>
	
	<h5>tests</h5>
	<?php 
		
		echo 'DROP TABLE : <hr>';
		var_dump(Voiture::drop());
		echo 'CREATE TABLE : <hr>';
		var_dump(Voiture::create());
		echo 'INSERT : <hr>';
		$nissan = new Voiture();
		$nissan->marque = "Nissan Pixo";
		$nissan->vitesse = 60;
		$nissan->save();
		$id = $nissan->id;
		echo 'Saved : '.$id.'<br>';
		echo 'GETBYID : <hr>';
		$other = Voiture::getById($id);
		var_dump($other);
		echo 'UPDATE : <hr>';
		$other->marque='tsoin';
		$other->save();
		var_dump($other);
		echo 'DELETE : <hr>';
		Voiture::deleteById($id);
		var_dump(Voiture::getById($id));
		echo 'POPULATE : <hr>';
		var_dump(Voiture::populate());
		echo 'CHANGE : <hr>';
		var_dump(Voiture::change(array('marque'=>'patate'),array('id'=>14)));
		
	?>
	
<?php
}

function test_plugin_install($id){
	if($id != 'fr.idleman.modele') return;
	require_once('Voiture.class.php');
	//Création de la table voiture
	Voiture::create();
	//Création d'une voiture d'exemple
	$pixo = new Voiture();
	$pixo->marque = "Nissan Pixo";
	$pixo->vitesse = 110;
	$pixo->save();
	// en cas d'erreur : throw new Exception('Mon erreur');
}
function test_plugin_uninstall($id){
	if($id != 'fr.idleman.modele') return;
	require_once('Voiture.class.php');
	Voiture::drop();
	// en cas d'erreur : throw new Exception('Mon erreur');
}

function test_plugin_section(&$sections){
	$sections['modele'] = 'Gestion du plugin Modèle';
}

Plugin::addCss("/main.css"); 
Plugin::addJs("/main.js"); 

Plugin::addHook("install", "test_plugin_install");
Plugin::addHook("uninstall", "test_plugin_uninstall"); 
Plugin::addHook("section", "test_plugin_section");
Plugin::addHook("menu_main", "test_plugin_menu"); 
Plugin::addHook("page", "test_plugin_page");  
Plugin::addHook("action", "test_plugin_action");    

?>