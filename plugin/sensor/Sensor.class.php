<?php

/*
 @nom: Sensor
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Classe de gestion des senseur de propise (humidité, température, luminosité etc...)
 */


class Sensor extends Entity{
	public $id,$location,$label,$uid,$ip;
	protected $TABLE_NAME = 'plugin_propise_sensor';
	protected $fields = 
	array(
		'id'=>'key',
		'label'=>'string',
		'uid'=>'string',
		'location'=>'string',
		'ip'=>'string'
	);



	function __construct(){
		parent::__construct();
	}

}

?>