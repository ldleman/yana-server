<?php

/**
* Classe de gestion SQL de la table radiorelay liée à la classe RadioRelay 
* @author: valentin carruesco <idleman@idleman.fr>
*/

//La classe RadioRelay hérite de SQLiteEntity qui lui ajoute des méthode de gestion de sa table en bdd (save,delete...)
class RadioRelay extends SQLiteEntity{

	public $name,$description,$pin,$room,$pulse,$id,$offCommand,$onCommand,$icon,$radiocode,$state; //Pour rajouter des champs il faut ajouter les variables ici...
	protected $TABLE_NAME = 'plugin_radiorelay'; 	//Pensez à mettre le nom de la table sql liée a cette classe
	protected $CLASS_NAME = 'radiorelay'; //Nom de la classe courante
	protected $object_fields = 
	array( // Ici on définit les noms des champs sql de la table et leurs types
		'name'=>'string',
		'onCommand'=>'string',
		'offCommand'=>'string',
		'description'=>'string',
		'radiocode'=>'int',
		'pin'=>'int',
		'room'=>'int',
		'icon'=>'string',
		'pulse'=>'int',
		'state'=>'int',
		'id'=>'key'
	);

	function __construct(){
		parent::__construct();
	}

}

?>