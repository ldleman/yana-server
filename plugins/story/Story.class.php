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
		require_once(dirname(__FILE__).'/Cause.class.php');
		
		global $conf;
		$causeManager = new Cause();
		/*$stories = array();
	
		switch($trigger->type){
			case 'time':
				
			break;
			case 'talk':
				$cause = new Cause();
				$cause = $causeManager->getById($trigger->value);
				$stories[]  = $cause->story;
			break;
		}
		
		
		foreach($stories as $story){
		*/	
		$storyCauses = $causeManager->loadAll(array(/*'story'=>$story*/));
			
			
		$sentence = $conf->get('last_sentence','var');
		list($i,$h,$d,$m,$y) = explode('-',date('i-H-d-m-Y'));
		$validCauses = array();
		
		
		
		foreach($storyCauses as $storyCause){
			$values = $storyCause->getValues();
			switch ($storyCause->type){
				case 'listen':
					if($values->value == $sentence)
						$validCauses[$storyCause->story][] = $storyCause;
				break;
				case 'time':
						;
						
						if ($storyCause->value != $i.'-'.$h.'-'.$d.'-'.$m.'-'.$y) $validate = false;
						if ((
							($i == $values->minut || $values->minut == '*') && 
							($h == $values->hour || $values->hour == '*') && 
							($d == $values->day || $values->day == '*') && 
							($m == $values->month || $values->month == '*') && 
							($y == $values->year || $values->year == '*')
							)){
							
								$validCauses[$storyCause->story][] = $storyCause;
							}
				break;
				case 'readvar':
						if ($conf->get($storyCause->target,'var') == $storyCause->value) 
							$validCauses[$storyCause->story][] = $storyCause;
				break;
			}
		}
	
		foreach($validCauses as $story=>$causes){
		
			if(count($causes) == $causeManager->rowCount(array('story'=>$story)))
				self::execute($story);
			
			
		}

			
	}
	
	
	
	public static function execute($story){
			global $conf;
			Functions::log('Execute story '.$story);
			require_once(dirname(__FILE__).'/Effect.class.php');
			$effectManager = new Effect();
			$effects = $effectManager->loadAll(array('story'=>$story),'sort');
			foreach($effects as $effect){
				$data = $effect->getValues();
				Functions::log($effect->type);
				switch ($effect->type) {
					case 'command':
						System::commandSilent($data->value);
					break;
					case 'var':
						$conf->put($data->var,$data->value,'var');
					break;
					case 'url':
						file_get_contents($data->value);
					break;
					case 'gpio':
						$pins = explode(',',$data->gpio);
						foreach($pins as $pin)
							Gpio::write($pin,$data->value,true);
					break;
					case 'sleep':
						if(is_numeric($data->value))
							sleep($data->value);
					break;
					case 'talk':
						try{
							$cli = new Client();
							$cli->connect();
							$cli->talk($data->value);
							$cli->disconnect();
						}catch(Exception $e){
							Functions::log("Story (talk) -> Connexion au serveur socket impossible : ".$e->getMessage());
						}
					break;
					case 'story':
						self::execute($data->value);
					break;
					default:
						
					break;
				}
			}
	}
}

?>