<?php

/*
 @nom: Sensor
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Classe de gestion des senseur de propise (humidité, température, luminosité etc...)
 */

class Sensor extends SQLiteEntity{

	public $id,$location,$label,$uid;
	protected $TABLE_NAME = 'plugin_propise_sensor';
	protected $CLASS_NAME = 'Sensor';
	protected $object_fields = 
	array(
		'id'=>'key',
		'label'=>'string',
		'uid'=>'string',
		'location'=>'string'
	);

	function __construct(){
		parent::__construct();
	}


	

}

?>