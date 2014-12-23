<?php

/**
* Classe de simulation de la personalité (actuellement uniquement du random sur les réponses)
* @author Idleman
* @todo Intégrer de l'IA
*/

class Personality{
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
									)
								);
	public static function response($type){
		return static::$sentences[$type][rand(0,count(static::$sentences[$type])-1)];
	}

}

?>