<?php


//Cette fonction va generer un nouveau element dans le menu
function story_plugin_menu(&$menuItems){
	global $_;
	$menuItems[] = array(
	'sort'=>2,
	'url'=>'index.php?module=story',
	'label'=>'Scénarios',
	'icon'=>'caret-square-o-right'
	);
}



//Cette fonction va generer une page quand on clique sur story dans menu
function story_plugin_page(){
	global $_;
	if(!isset($_['module']) || $_['module']!='story') return;
	require_once(__DIR__.'/Story.class.php');
	require_once(__DIR__.'/Cause.class.php');
	require_once(__DIR__.'/Effect.class.php');
		
	$page = isset($_['action']) && in_array($_['action'], array('edit','documentation')) ? $_['action']: 'list'; 
	require_once(__DIR__.'/'.$page.'.php');

}

function story_plugin_install($id){
	if($id != 'fr.idleman.story') return;
	Entity::install(__DIR__);
}

function story_plugin_uninstall($id){
	if($id != 'fr.idleman.story') return;
	Entity::uninstall(__DIR__);
}

function story_plugin_section(&$sections){
	$sections['story'] = 'Gestion du plugin Scénario';
}


//cette fonction comprends toutes les actions du plugin qui ne nécessitent pas de vue html
function story_plugin_action(){
	require_once(__DIR__.'/action.php');
}



function story_vocal_command(&$response,$actionUrl){
	global $conf;
	require_once(__DIR__.'/Cause.class.php');
	$causeManager = new Cause();
	$vocals = $causeManager->loadAll(array('type'=>'listen'));
	foreach($vocals as $vocal){
		$data = json_decode($vocal->values);
		
		$data->confidence = $data->confidence==0 ? '0.81' : $data->confidence;
		$response['commands'][] = array(
		'command'=>$conf->get('VOCAL_ENTITY_NAME').' '.$data->value,
		'url'=>$actionUrl.'?action=plugin_story_check&type=talk&sentence='.urlencode($data->value),'confidence'=>($data->confidence+$conf->get('VOCAL_SENSITIVITY'))
		);
	}
}

function plugin_story_check(){
	require_once(__DIR__.'/Story.class.php');
	Story::check();
}


function story_gpio_change($pin,$state){
	require_once(__DIR__.'/Story.class.php');
	Story::check(array('type'=>'gpio','pin'=>$pin,'state'=>$state));
}

Plugin::addCss("/main.css"); 
Plugin::addJs("/main.js"); 

Plugin::addHook("install", "story_plugin_install");
Plugin::addHook("uninstall", "story_plugin_uninstall"); 
Plugin::addHook("section", "story_plugin_section");
Plugin::addHook("menu_main", "story_plugin_menu"); 
Plugin::addHook("page", "story_plugin_page");  
Plugin::addHook("action", "story_plugin_action");    


?>