<?php
/*
@name Story
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@description [EN CONSTRUCTION - NE PAS TENIR COMPTE] Plugin de gestion des scénarios avec leurs causes et leurs effets
*/


include('Story.class.php');
include('Cause.class.php');
include('Effect.class.php');


function story_plugin_menu(&$menuItems){
	global $_;
	$menuItems[] = array('sort'=>10,'content'=>'<a href="index.php?module=story"><i class="fa fa-caret-square-o-right"></i> Scénarios</a>');
}

function story_plugin_page($_){
	if(isset($_['module']) && $_['module']=='story'){
		switch(@$_['action']){
			case 'edit':
				require_once('edit.php');
			break;
			default:
				require_once('list.php');
			break;
		}
	}
}


function story_check($event =false){
	require_once('Cause.class.php');
	require_once('Effect.class.php');
	global $conf;
	

	$causeManager = new Cause();
	$effectManager = new Effect();

	$causes = array();

		$storyCauses = $causeManager->loadAll(array('story'=>$event->story));
		$validate = $event->story;
		foreach($storyCauses as $storyCause){
			switch ($storyCause->type) {
				case 'listen':
					if($event->type == $storyCause->type){
						if($storyCause->value != $event->value){
							$validate = false;
						}
					}
				break;
				case 'time':

					list($d,$m,$y,$h,$i) = explode('-',date('d-m-Y-H-i'));
					
					if ($storyCause->value != $i.'-'.$h.'-'.$d.'-'.$m.'-'.$y) $validate = false;
				break;
				case 'readvar':
					if ($conf->get($storyCause->target,'var') != $storyCause->value) $validate = false;
				break;

			}
		}

		

		if($validate!=false){

			//consequences
			$effects = $effectManager->loadAll(array('story'=>$event->story));
			foreach($effects as $effect){
				switch ($effect->type) {
					case 'command':
						exec($effect->value);
					break;
					case 'var':
						$conf->put($effect->target,$effect->value,'var');
					break;
					case 'actuator':
						file_get_contents('action.php?action='.$effect->value);
					break;
					case 'sleep':
						sleep ($effect->value);
					break;
					case 'talk':
						if(!file_exists('story-event-file'))
						touch('story-event-file');
						$d = json_decode(file_get_contents('story-event-file'),true);
						$d[] = $effect->value;
						file_put_contents('story-event-file', json_encode($d));
					break;
				}
			}
		}

	
	

}


function story_plugin_event(&$response){
	
		$d = json_decode(file_get_contents('story-event-file'),true);
		foreach($d as $talk){
		$response['responses']= array(
								array('type'=>'talk','sentence'=>$talk)
							);
		}
		unlink('story-event-file');
	
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
		require_once('Cause.class.php');
		$vocal = new Cause();
		$vocal = $vocal->getById($_['event']);
		
		story_check($vocal);
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
	require_once('Cause.class.php');
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
Plugin::addHook("cron", "story_check");
?>