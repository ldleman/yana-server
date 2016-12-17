<?php

/*
 @nom: Modele
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Modele de classe pour les plugins
 */

//Ce fichier permet de gerer vos donnees en provenance de la base de donnees

//Il faut changer le nom de la classe ici (je sens que vous allez oublier)
class Modele extends Entity{
	public $id,$string,$integer; //Pour rajouter des champs il faut ajouter les variables ici...
	protected $TABLE_NAME = 'plugin_modele'; 	//Penser a mettre le nom du plugin et de la classe ici
	protected $fields = 
	array( //...Puis dans l'array ici mettre nom du champ => type
		'id'=>'key',
		'string'=>'string',
		'integer'=>'int'
	);

	function __construct(){
		parent::__construct();
	}
}

?>