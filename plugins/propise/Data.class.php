<?php

/*
 @nom: Data
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Classe de gestion des données de porpyse (humidité, température, luminosité etc...)
 */

class Data extends SQLiteEntity{

	public $id,$light,$sound,$humidity,$temperature,$mouvment,$time,$location;
	protected $TABLE_NAME = 'plugin_propise';
	protected $CLASS_NAME = 'Data';
	protected $object_fields = 
	array(
		'id'=>'key',
		'light'=>'string',
		'sound'=>'string',
		'humidity'=>'string',
		'temperature'=>'string',
		'mouvment'=>'string',
		'time'=>'string',
		'location'=>'string'
	);

	function __construct(){
		parent::__construct();
	}


	

}

?>