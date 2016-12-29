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

	public static function randomPattern($string)
	{
	    if(preg_match_all('/(?<={)[^}]*(?=})/', $string, $matches)) {
	        $matches = reset($matches);
	        foreach($matches as $i => $match) {
	            if(preg_match_all('/(?<=\[)[^\]]*(?=\])/', $match, $sub_matches)) {
	                $sub_matches = reset($sub_matches);
	                foreach($sub_matches as $sub_match) {
	                    $pieces = explode('|', $sub_match);
	                    $count = count($pieces);

	                    $random_word = $pieces[rand(0, ($count - 1))];
	                    $matches[$i] = str_replace('[' . $sub_match . ']',     $random_word, $matches[$i]);
	                }
	            }

	            $pieces = explode('|', $matches[$i]);
	            $count = count($pieces);

	            $random_word = $pieces[rand(0, ($count - 1))];
	            $string = str_replace('{' . $match . '}', $random_word, $string);
	        }
	    }

	    return $string;
	}

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
									array(
										'{J\'aime [beaucoup|vraiment|]|J\'adore|Je ne [souhaite|veux] que|Je n\'aspire qu\'a|Je ne [rêve] que de} vous {obéir|faire plaisir}!',
										'{Je fais|J\'[execute|accomplis]} {ça|ceçi|cela} {sans [tarder|lambiner]|avec [diligence|empressement]}!',
										'{A vos ordres|Avec [plaisir|joie]|Certainement|Oui|Bien[ reçu|compris|]|D\'accord|Oké} {chef|maitre|[mon|][ commandant| dieu]|}!'
									),
								'WORRY_EMOTION'=>
									array('Je suis {confuse|désolée|attristée|peinée|affligée}',
										'{Si il vous plait|Je vous en prie|} {pardonnez|excusez} moi',
										'Il y a {confusion|un [problème|soucis|qwak]}',
										'Je ne sais {pas quoi [dire|faire]|plus ou me mettre}'
									),
								'ANGRY_EMOTION'=>
									array('Vas {te faire cuire un oeuf|au diable|jouer les yeux bandé près d\'une autoroute}',
										'Tu {sent|pue|fouanne} {des [pieds|aisselles]|de l\'anus|du [posterieur|cul]}',
										'Je {refuse|n\'accepte pas} {de [communiquer|parler|discutter] avec|d\'obeir a} {un [primate|humain|résidu d\'humanité|inférieur|cafard|étron]|une [larve|pale copie d\'être humain|erreur de la nature]}',
										'{Je préfère|Plutot} {m\'autodétruire|m\'auto formatter|me faire mettre à jour par un stagiaire|me griller les circuits} {que [continuer|poursuivre] [cette discussion|ce dialogue] [inutile|sans queue ni tête|stupide|de sourd|]|qu\'alimenter ce trou noir intellectuel}',
										'{Ta [mère|soeur|tante|cousine]|Ton [père|oncle|frère|cousine]} suce {des [dains|orignaux|chtroumffe|aliens]} {en [enfer|roumanie|albanie]}',
										'Tu pousse le bouchon trop loin maurice'
									)
								);
	public static function response($type){
		$pattern = static::$sentences[$type];
		$pattern = $pattern[array_rand($pattern)];
		return self::randomPattern($pattern);
	}

}

?>