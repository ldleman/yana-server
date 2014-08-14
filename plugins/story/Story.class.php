<?php

/*
 @nom: Story
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Représente un scénario avec ses causes de déclenchement et ses effets associés
 */

class Story extends SQLiteEntity{

	public $id,$date,$user,$label,$state;
	protected $TABLE_NAME = 'plugin_story';
	protected $CLASS_NAME = 'Story';
	protected $object_fields = 
	array(
		'id'=>'key',
		'date'=>'string',
		'user'=>'int',
		'label'=>'string',
		'state'=>'int'
	);

	function __construct(){
		parent::__construct();
	}
	
	

	public static function check($event =false){
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
					list($i,$h,$d,$m,$y) = explode('-',date('i-H-d-m-Y'));
					list($i2,$h2,$d2,$m2,$y2) = explode('-',$storyCause->value);
					if ($storyCause->value != $i.'-'.$h.'-'.$d.'-'.$m.'-'.$y) $validate = false;
					if (!(
								($i == $i2 || $i2 == '*') && 
								($h == $h2 || $h2 == '*') && 
								($d == $d2 || $d2 == '*') && 
								($m == $m2 || $m2 == '*') && 
								($y == $y2 || $y2 == '*')
							)){
								$validate = false;
						}


				break;
				case 'readvar':
					if ($conf->get($storyCause->target,'var') != $storyCause->value) $validate = false;
				break;

			}
		}

		

		if($validate!=false){

			//consequences
			$effects = $effectManager->loadAll(array('story'=>$event->story),'sort');
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
						if(is_numeric($effect->value))
							sleep ($effect->value);
					break;
					case 'talk':
						$clientManager = new Client();
						$clients = $clientManager->populate();
						foreach($clients as $client){
							$client->talk($effect->value);
						}
					break;
				}
			}
		}

	

		/*
		$manager = new Story();

		//get all dictionnary values
		$query = '
		SELECT * FROM plugin_story_cause WHERE story = (SELECT story.id FROM plugin_story story 
		LEFT JOIN plugin_story_cause cause ON story.id = cause.story)';

		$storyToCheck = array();
		
		$results = $manager->customQuery($query);
		while($result = $results->fetchArray()){
			$storyToCheck[$result['story']][] = $result;
		}
		
		if(count($storyToCheck)!=0){
			foreach($storyToCheck as $story=>$causes){
				//On part du principe que le scénario est validé tant qu'aucune cause manquante ne viens la contredire
				$activateStory = true;
				//On vas checker toutes les causes de ce scenario 
				foreach($causes as $cause){
					$validateCause = false;
					switch($cause['type']){
						case 'listen':
							if($cause['value'] == $dic['lastphrase'])
								$validateCause = true;
							if($cause['operator']=='!=') $validateCause = !$validateCause;
						break;
						case 'readvar':
						break;
						case 'event':
						break;
						case 'time':
							list($i2,$h2,$d2,$m2,$y2) = explode('-',$cause['value']);
							if (
								($i == $i2 || $i2 == '*') && 
								($h == $h2 || $h2 == '*') && 
								($d == $d2 || $d2 == '*') && 
								($m == $m2 || $m2 == '*') && 
								($y == $y2 || $y2 == '*')
							)
								$validateCause = true;
							
							if($cause['operator']=='!=') $validateCause = !$validateCause;
							
						break;
					}
					$activateStory = $activateStory && $validateCause;
					
					var_dump($cause);
				}
				if($activateStory) var_dump('story validated !');
			}
		
		}*/
	}

}

?>