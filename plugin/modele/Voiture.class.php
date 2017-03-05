<?php

/*
 @nom: Voiture
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Modele de classe pour les plugins
 */

//Ce fichier permet de gerer vos donnees en provenance de la base de donnees

//Il faut changer le nom de la classe ici (je sens que vous allez oublier)
class Voiture extends Entity{

	
	public $id,$marque,$vitesse; //Pour rajouter des champs il faut ajouter les variables ici...
	protected $TABLE_NAME = 'plugin_voiture'; 	//Penser a mettre le nom du plugin et de la classe ici
	protected $fields = 
	array( //...Puis dans l'array ici mettre nom du champ => type
		'id'=>'key',
		'marque'=>'string',
		'vitesse'=>'int'
	);



}

?>