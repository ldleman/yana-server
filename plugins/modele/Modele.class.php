<?php

/*
 @nom: Modele
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Modele de classe pour les plugins
 */

//Ce fichier permet de gerer vos donnees en provenance de la base de donnees

//Il faut changer le nom de la classe ici (je sens que vous allez oublier)
class Modele extends SQLiteEntity{

	
	protected $id,$string,$integer; //Pour rajouter des champs il faut ajouter les variables ici...
	protected $TABLE_NAME = 'plugin_modele'; 	//Penser a mettre le nom du plugin et de la classe ici
	protected $CLASS_NAME = 'modele';
	protected $object_fields = 
	array( //...Puis dans l'array ici mettre nom du champ => type
		'id'=>'key',
		'string'=>'string',
		'integer'=>'int'
	);

	function __construct(){
		parent::__construct();
	}
//Methodes pour recuperer et modifier les champs (set/get)
	function setId($id){
		$this->id = $id;
	}
	
	function getId(){
		return $this->id;
	}

	function getString(){
		return $this->string;
	}

	function setString($string){
		$this->string = $string;
	}

	function getInteger(){
		return $this->integer;
	}

	function setInteger($integer){
		$this->integer = $integer;
	}

}

?>