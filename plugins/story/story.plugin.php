<?php
/*
@name Story
@author Valentin CARRUESCO <idleman@idleman.fr>
@link http://blog.idleman.fr
@licence CC by nc sa
@version 1.0.0
@client 2
@description [BETA] Plugin de gestion des scénarios avec leurs causes et leurs effets
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
	Story::check();
}


function story_gpio_change($pin,$state){
	
	require_once('Story.class.php');
	Story::check(array('type'=>'gpio','pin'=>$pin,'state'=>$state));
}

function story_plugin_action(){
	global $_,$myUser;
	switch($_['action']){
		
	case 'plugin_story_get_type_template':
		Action::write(
				function($_,&$response){
					$templates = array_merge(Cause::types(),Effect::types());
					$template = $templates[$_['type']];
					
					$_['data']['value'] = stripslashes($_['data']['value']);
					
					preg_match_all("/(\{)(.*?)(\})/", $template['template'], $matches, PREG_SET_ORDER);
					foreach($matches as $match){
						$template['template'] = str_replace($match[0],$_['data'][$match[2]],$template['template']);
					}
					
					$response['html'] = 
					'<div data-element="line" class="line" data-type="'.$_['type'].'">
						<i class="fa '.$template['icon'].'"></i> <strong>'.$template['label'].'</strong> '.$template['template'].' <div class="delete"><i onclick="deleteLine(this);" class="fa fa-times"></i></div>
					</div>';
				},
				array()
			);
	break;
	
	
	case 'plugin_story_import':
	
		$story = new Story();
		$cause = new Cause();
		$effect = new Effect();
		
		$datas = json_decode(file_get_contents($_FILES['import']['tmp_name']),true);

		if(!$datas) exit('Mauvais format de données');
		
		$story = $story->fromArray($datas['story']);
		unset($story->id);
	
		$story->save();
		
		foreach($datas['causes'] as $data):
			$newcause = $cause->fromArray($data);
			unset($newcause->id);
			$newcause->story = $story->id;
			$newcause->save();
		endforeach;
		
		foreach($datas['effects'] as $data):
			$neweffect = $effect->fromArray($data);
			unset($neweffect->id);
			$neweffect->story = $story->id;
			$neweffect->save();
		endforeach;
		header('location: index.php?module=story');
		
	break;
	case 'plugin_story_export':
	
		$story = new Story();
		$cause = new Cause();
		$effect = new Effect();
		
		$story = $story->getById($_['id']);
		$effects = $effect->loadAll(array('story'=>$story->id),'sort');
		$causes = $cause->loadAll(array('story'=>$story->id),'sort');
		
		$json = array();
		$json['story'] = $story->toArray();
		
		foreach($causes as $cause):
			$json['causes'][] = $cause->toArray();
		endforeach;
		
		foreach($effects as $effect):
			$json['effects'][] = $effect->toArray();
		endforeach;
		
		header('Content-Description: File Transfer');
	    header('Content-Type: application/json');
	    header('Content-Disposition: attachment; filename=scenario-'.$story->id.'-'.date('d-m-Y').'.json');
	    header('Content-Transfer-Encoding: binary');
	    header('Expires: 0');
	   	header('Cache-Control: must-revalidate');
	    header('Pragma: public');
		echo json_encode($json);
	break;
	
	case 'plugin_story_get_causes_effects':
		Action::write(
				function($_,&$response){
					
					$cause = new Cause();
					$effect = new Effect();
					$effects = $effect->loadAll(array('story'=>$_['id']),'sort');
					$causes = $cause->loadAll(array('story'=>$_['id']),'sort');
					$response['results'] = array('causes'=>array(),'effects'=>array());
					foreach($causes as $caus){
						$data = $caus->getValues();
						$response['results']['causes'][]= array(
						'type' => $caus->type,
						'panel'=>"CAUSE",
						'data'=> $data
						);
					}
			
					foreach($effects as $eff){
						$data = $eff->getValues();
						$response['results']['effects'][]=array( 
						'type' => $eff->type,
						'panel'=>"EFFECT",
						'data'=> $data
						);
					}
				},
				array()
			);
	break;
	
	case 'plugin_story_get_captors_plugins':
		
		Action::write(
				function($_,&$response){
					$deviceManager = new Device();
					$devices = $deviceManager->loadAll(array('state'=>1,'type'=>Device::CAPTOR));
					$response['plugins'] = array();
					foreach($devices as $device){
						if(!isset($response['plugins'][$device->plugin])) $response['plugins'][] = $device->plugin ;
					}
				},
				array()
			);
	break;
	
	case 'plugin_story_get_captors':
		
		Action::write(
				function($_,&$response){
					$deviceManager = new Device();
					$devices = $deviceManager->loadAll(array('state'=>1,'plugin'=>$_['plugin'],'type'=>Device::CAPTOR));
				
					foreach($devices as $device){
						$response['devices'][] = array(
							'plugin' => $device->plugin,
							'label' => $device->label,
							'id' => $device->id
						);
					}
				},
				array()
			);
	break;
	
	case 'plugin_story_get_captor_values':
		
		Action::write(
				function($_,&$response){
					$deviceManager = new Device();
					$device = $deviceManager->getById($_['id']);
					$response['values'] = $device->getValues();
				},
				array()
			);
	break;
	
	case 'plugin_story_launch_story':
		Action::write(
			function($_,&$response){
				Story::execute($_['id']);
				$story = new Story();
				$story = $story->getById($_['id']);
				$response['log'] = $story->log;
			},
			array()
		);
	break;
	
	case 'plugin_story_change_state':
		Action::write(
			function($_,&$response){
				$story = new Story();
				
				$story->change(array('state'=>$_['state']),array('id'=>$_['id']));
			},
			array()
		);
	break;
	
	case 'plugin_story_delete_story':
		Action::write(
			function($_,&$response){
				$storyManager = new Story();
				$causeManager = new Cause();
				$effectManager = new Effect();
				$storyManager->delete(array('id'=>$_['id']));
				$causeManager->delete(array('story'=>$_['id']));
				$effectManager->delete(array('story'=>$_['id']));
			},
			array()
		);
	break;

	case 'plugin_story_check':
		require_once(dirname(__FILE__).'/Story.class.php');
		global $conf;
		$conf->put('last_sentence',urldecode($_['sentence']),'var');
		//plugin_story_check();
		Story::check(array('type'=>'sentence','sentence'=>urldecode($_['sentence'])));
	break;

	case 'plugin_story_save_story':

	Action::write(
		function($_,&$response){
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
			
			foreach($_['story']['causes'] as $cause){
				$current = new Cause();
				$current->type = $cause['type'];
				$current->operator = @$cause['operator'];
				$current->setValues($cause);
				$current->sort = $i;
				$current->union = $cause['union'];
				$current->story = $story->id;
				$current->save();
				$i++;
			}
			
			$i = 0;
			
			
			foreach($_['story']['effects'] as $effect){
				$current = new Effect();
				$current->type = $effect['type'];
				$current->setValues($effect);
				$current->sort = $i;
				$current->union = $cause['union'];
				$current->story = $story->id;
				$current->save();
				$i++;
			}
		
		},
		array()
	);

	break;
	}
}



function story_vocal_command(&$response,$actionUrl){
	global $conf;
	require_once(dirname(__FILE__).'/Cause.class.php');
	$causeManager = new Cause();
	$vocals = $causeManager->loadAll(array('type'=>'listen'));
	foreach($vocals as $vocal){
		$data = json_decode($vocal->values);
		
		$response['commands'][] = array(
		'command'=>$conf->get('VOCAL_ENTITY_NAME').' '.$data->value,
		'url'=>$actionUrl.'?action=plugin_story_check&type=talk&sentence='.urlencode($data->value),'confidence'=>('0.90'+$conf->get('VOCAL_SENSITIVITY'))
		);
	}
}

Plugin::addCss("/css/main.css"); 
Plugin::addJs("/js/main.js"); 

Gpio::listen('all','story_gpio_change');
Plugin::addHook("menubar_pre_home", "story_plugin_menu");  
Plugin::addHook("home", "story_plugin_page");  
Plugin::addHook("action_post_case", "story_plugin_action");
Plugin::addHook("vocal_command", "story_vocal_command");
Plugin::addHook("cron", "plugin_story_check");
?>
