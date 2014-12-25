<?php
/*
@name Story
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@description [EN CONSTRUCTION - NE PAS TENIR COMPTE] Plugin de gestion des scénarios avec leurs causes et leurs effets
*/


include(dirname(__FILE__).'/Story.class.php');
include(dirname(__FILE__).'/Cause.class.php');
include(dirname(__FILE__).'/Effect.class.php');


function story_plugin_menu(&$menuItems){
	global $_;
	$menuItems[] = array('sort'=>10,'content'=>'<a href="index.php?module=story"><i class="fa fa-caret-square-o-right"></i> Scénarios</a>');
}

function story_plugin_page($_){
	if(isset($_['module']) && $_['module']=='story'){
		switch(@$_['action']){
			case 'edit':
				require_once(dirname(__FILE__).'/edit.php');
			break;
			default:
				require_once(dirname(__FILE__).'/list.php');
			break;
		}
	}
}

function plugin_story_check(){
	require_once('Story.class.php');
	require_once('Cause.class.php');
	$time = new Cause();
	$time->type = "time";
	$time->value = date('i-h-d-m-Y');
	Story::check($time);
}


function story_plugin_action(){
	global $_,$myUser;
	switch($_['action']){
	
	case 'DELETE_STORY':
		$storyManager = new Story();
		$causeManager = new Cause();
		$effectManager = new Effect();
		$storyManager->delete(array('id'=>$_['id']));
		$causeManager->delete(array('story'=>$_['id']));
		$effectManager->delete(array('story'=>$_['id']));
	break;

	case 'plugin_story_check':
		require_once(dirname(__FILE__).'/Cause.class.php');
		$vocal = new Cause();
		$vocal = $vocal->getById($_['event']);
		
		Story::check($vocal);
	break;

	case 'SAVE_STORY':
	
		$causeManager = new Cause();
		$effectManager = new Effect();
		$story = new Story();
		if(isset($_['story']['id']) && $_['story']['id']!='0'){
			$story = $story->getById($_['story']['id']);
			$causeManager->delete(array('story'=>$story->id));
			$effectManager->delete(array('story'=>$story->id));
		}
		
		$story->label = $_['story']['label'];
		$story->date = time();
		$story->state = 1;
		$story->save();
	
		$i = 0;
		
		foreach($_['story']['cause'] as $cause){
			$current = new Cause();
			$current->type = $cause['type'];
			$current->target = is_array(@$cause['target'])?implode('|',@$cause['target']):@$cause['target'];
			$current->operator = @$cause['operator'];
			$current->value = $cause['value'];
			$current->sort = $i;
			$current->union = $cause['union'];
			$current->story = $story->id;
			$current->save();
			$i++;
		}
		
		$i = 0;
		foreach($_['story']['effect'] as $effect){
			$current = new Effect();
			$current->type = $effect['type'];
			$current->target = is_array(@$effect['target'])?implode('|',@$effect['target']):@$effect['target'];
			$current->value = $effect['value'];
			$current->sort = $i;
			$current->union = $cause['union'];
			$current->story = $story->id;
			$current->save();
			$i++;
		}
		
		
	break;
	}
}





function story_vocal_command(&$response,$actionUrl){
	global $conf;
	require_once(dirname(__FILE__).'/Cause.class.php');
	$causeManager = new Cause();
	$vocals = $causeManager->loadAll(array('type'=>'listen'));
	foreach($vocals as $vocal){
		$response['commands'][] = array(
		'command'=>$conf->get('VOCAL_ENTITY_NAME').' '.$vocal->value,
		'url'=>$actionUrl.'?action=plugin_story_check&type=talk&event='.$vocal->id,'confidence'=>('0.90'+$conf->get('VOCAL_SENSITIVITY'))
		);
	}
}

Plugin::addCss("/css/main.css"); 
Plugin::addJs("/js/main.js"); 

Plugin::addHook("menubar_pre_home", "story_plugin_menu");  
Plugin::addHook("home", "story_plugin_page");  
Plugin::addHook("action_post_case", "story_plugin_action");
Plugin::addHook("vocal_command", "story_vocal_command");
Plugin::addHook("cron", "plugin_story_check");
?>