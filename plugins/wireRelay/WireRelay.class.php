<?php

/**
* Classe de gestion SQL de la table wirerelay liée à la classe Wirerelay 
* @author: valentin carruesco <idleman@idleman.fr>
*/

//La classe Wirerelay hérite de SQLiteEntity qui lui ajoute des méthode de gestion de sa table en bdd (save,delete...)
class Wirerelay extends SQLiteEntity{

	public $name,$description,$pin,$room,$pulse,$id,$offCommand,$onCommand; //Pour rajouter des champs il faut ajouter les variables ici...
	protected $TABLE_NAME = 'plugin_wirerelay'; 	//Pensez à mettre le nom de la table sql liée a cette classe
	protected $CLASS_NAME = 'wirerelay'; //Nom de la classe courante
	protected $object_fields = 
	array( // Ici on définit les noms des champs sql de la table et leurs types
		'name'=>'string',
		'onCommand'=>'string',
		'offCommand'=>'string',
		'description'=>'string',
		'pin'=>'int',
		'room'=>'int',
		'pulse'=>'int',
		'id'=>'key'
	);

	function __construct(){
		parent::__construct();
	}

}

?>