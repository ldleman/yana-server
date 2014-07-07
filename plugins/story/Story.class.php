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
	
	
	
	public static function check(){
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
		
		}
	}
}

?>