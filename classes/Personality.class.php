<?php

/**
* Classe de simulation de la personalité (actuellement uniquement du random sur les réponses)
* @author Idleman
* @todo Intégrer de l'IA
*/

class Personality extends SQLiteEntity{


	 protected $id,$key,$value;
	 protected $TABLE_NAME = 'personnality';
	 protected $CLASS_NAME = 'Personality';
	 protected $object_fields = 
	    array(
		    'id'=>'key',
		    'key'=>'string',
            'value'=>'longstring'
	    );


	public function birth(){
		$this->put('birthday',strtotime('-'.rand(0,50).' years'));
		$this->put('favorite_color',Functions::array_rand(array('orange','rouge','bleu','vert','violet','taupe','indigo','bordeaux','jaune','gris','noir','blanc','citron'),1));
		$this->put('favorite_book',Functions::array_rand(array('Les anales du disque monde de Terry Pratchet','La trilogie des fourmis de Bernard Weber','Fondation d\'Isaac Asimov','Cosmétique de l\'ennemie d\'amélie nothomb','Tout sauf un home d\'Isaac Asimov','Le vieux et son implant de paul bera'),1));
		$this->put('favorite_food',Functions::array_rand(array('Le magret de canard','Les nuggets maison','Les calzones','les escalopes milanaises'),1));
		$this->put('favorite_movie',Functions::array_rand(array('Retour vers le futur 1,2 et 3','Fight Club','Vice et versa','Mary poppins'),1));
		$this->put('favorite_band',Functions::array_rand(array('Nirvana','Noir désir','Zoufris maracas','Les casseurs flowters','Les svinkels','Les frêres brothers','louis chédid','Maxime le forestier','Brassens'),1));
		$this->put('size',Functions::array_rand(array('Grande','Petite','Moyenne'),1));
		$this->put('skin',Functions::array_rand(array('Noire','Jaune','Blanche','Métisse'),1));
		$this->put('fear',rand(0,10));
		$this->put('anger',rand(0,10));
		$this->put('sadness',rand(0,10));
		$this->put('gluttony',rand(0,10));
		$this->put('lust',rand(0,10));
		$this->put('jealousy',rand(0,10));
	}

	public function put($key,$value){
		$attribute = $this->load(array('key'=>$key));
		if(!$attribute) $attribute = new Personality();
		$attribute->key = $key;
		$attribute->value = $value;
		$attribute->save();
	}
	public function get($key){
		$attribute = $this->load(array('key'=>$key));
		if(!$attribute) return '';
		return $attribute->value; 
	
	}


	public static $sentences = array(
								'ORDER_CONFIRMATION'=>
									array('A vos ordres!',
										'Bien!',
										'Oui commandant!',
										'Avec plaisir!',
										'J\'aime vous obéir!',
										'Avec plaisir!',
										'Certainement!',
										'Je fais ça sans tarder!',
										'Avec plaisir!',
										'Oui chef!'
									),
								'WORRY_EMOTION'=>
									array('Je suis confuse',
										'Je suis désolée',
										'Pardonnez moi',
										'Il y a confusion',
										'Je ne sais pas quoi dire'
									),
								'ANGRY_EMOTION'=>
									array('Vas te faire cuire un oeuf',
										'Tu sent des pieds',
										'Je refuse de communiquer avec un primate',
										'Je préfère m\'autodétruire que continuer cette discussion',
										'Tu pousse le bouchon trop loin maurice'
									)
								);
	public static function response($type){
		return static::$sentences[$type][rand(0,count(static::$sentences[$type])-1)];
	}

}

?>