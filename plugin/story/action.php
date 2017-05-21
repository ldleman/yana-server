<?php 
global $_,$conf;

switch($_['action']){
	case 'plugin_story_get_type_template':
	Action::write(
		function($_,&$response){
			require_once(__DIR__.SLASH.'Cause.class.php');
			require_once(__DIR__.SLASH.'Effect.class.php');
			$templates = array_merge(Cause::types(),Effect::types());
			$template = $templates[$_['type']];
			
			$_['data']['value'] = stripslashes($_['data']['value']);
			
			preg_match_all("/(\{)(.*?)(\})/", $template['template'], $matches, PREG_SET_ORDER);

			foreach($matches as $match){
				if(isset($_['data'][$match[2]]))
				$template['template'] = str_replace($match[0],$_['data'][$match[2]],$template['template']);
			}
			
			$response['html'] = 
			'<div data-element="line" class="line" data-type="'.$_['type'].'">
			<i class="fa '.$template['icon'].'"></i> <strong>'.$template['label'].'</strong> '.$template['template'].' <div class="delete"><i onclick="deleteLine(this);" class="fa fa-times"></i></div>
		</div>';
	},array());

	break;
	
	
	case 'plugin_story_import':
		try{
			require_once(__DIR__.SLASH.'Story.class.php');
			require_once(__DIR__.SLASH.'Cause.class.php');
			require_once(__DIR__.SLASH.'Effect.class.php');

			

			$datas = json_decode(file_get_contents($_FILES['import']['tmp_name']),true);

			if(!$datas) throw new Exception('Mauvais format de données');

			$story = Story::fromArray($datas['story']);
			unset($story->id);
			$story->save();

			foreach($datas['causes'] as $data):
				$newcause = Cause::fromArray($data);
				unset($newcause->id);
				$newcause->story = $story->id;
				$newcause->save();
			endforeach;

			foreach($datas['effects'] as $data):
				$neweffect = Effect::fromArray($data);
				unset($neweffect->id);
				$neweffect->story = $story->id;
				$neweffect->save();
			endforeach;

			$response = 'success=Scénario importé';	
		}catch(Exception $e){
			$response = 'error='.$e->getMessage();	
		}
		header('location: index.php?module=story&'.$response);
	
	break;

	case 'plugin_story_export':
		try{
			require_once(__DIR__.SLASH.'Story.class.php');
			require_once(__DIR__.SLASH.'Cause.class.php');
			require_once(__DIR__.SLASH.'Effect.class.php');
		
			$story = Story::getById($_['id']);
			if($story->id==0) throw new Exception("Scénario introuvable");
			
			$effects = Effect::loadAll(array('story'=>$story->id),'sort');
			$causes = Cause::loadAll(array('story'=>$story->id),'sort');

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
		}catch(Exception $e){
			header('location: index.php?module=story&error='.$e->getMessage());
		}
	break;
	
	case 'plugin_story_get_causes_effects':
		Action::write(
			function($_,&$response){
				require_once(__DIR__.SLASH.'Story.class.php');
				require_once(__DIR__.SLASH.'Cause.class.php');
				require_once(__DIR__.SLASH.'Effect.class.php');
				$response['results'] = 
				array(
					'causes' =>  array(),
					'effects' =>  array()
				);
				if($_['id']=='') return;
			
				$effects = Effect::loadAll(array('story'=>$_['id']),'sort');
				$causes = Cause::loadAll(array('story'=>$_['id']),'sort');


				$response['results'] = array('causes'=>array(),'effects'=>array());
				foreach($causes as $caus){
					$data = $caus->getValues();
					$response['results']['causes'][]= array(
						'type' => $caus->type,
						'panel'=>"cause",
						'data'=> $data
						);
				}

				foreach($effects as $eff){
					$data = $eff->getValues();
					$response['results']['effects'][]=array( 
						'type' => $eff->type,
						'panel'=>"effect",
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
			require_once(__DIR__.SLASH.'Story.class.php');
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
			require_once(__DIR__.SLASH.'Story.class.php');
			Story::change(array('state'=>$_['state']),array('id'=>$_['id']));
		},
		array()
		);
	break;
	
	case 'plugin_story_delete_story':
	Action::write(
		function($_,&$response){
			require_once(__DIR__.SLASH.'Story.class.php');
			require_once(__DIR__.SLASH.'Cause.class.php');
			require_once(__DIR__.SLASH.'Effect.class.php');

			Story::deleteById($_['id']);
			Cause::delete(array('story'=>$_['id']));
			Effect::delete(array('story'=>$_['id']));
		},
		array()
		);
	break;

	case 'plugin_story_check':
		require_once(__DIR__.SLASH.'Story.class.php');
		global $conf;
		$conf->put('last_sentence',urldecode($_['sentence']),'var');
		Story::check(array('type'=>'sentence','sentence'=>urldecode($_['sentence'])));
	break;

	case 'plugin_story_save_story':

		Action::write(
			function($_,&$response){
				require_once(__DIR__.SLASH.'Story.class.php');
				require_once(__DIR__.SLASH.'Cause.class.php');
				require_once(__DIR__.SLASH.'Effect.class.php');
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
				$response['id'] = $story->id;
				
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
		array());

	break;
}

?>