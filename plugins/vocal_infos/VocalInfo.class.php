<?php

/*
 @nom: VocalInfo
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Classe de gestion des informations vocales
 */

class VocalInfo extends SQLiteEntity{

	public $id,$text,$sound,$command,$day,$hour,$minut,$month,$year;
	protected $TABLE_NAME = 'plugin_vocalInfo';
	protected $CLASS_NAME = 'VocalInfo';
	protected $object_fields = 
	array(
		'id'=>'key',
		'text'=>'string',
		'sound'=>'string',
		'command'=>'string',
		'day'=>'string',
		'hour'=>'string',
		'minut'=>'string',
		'month'=>'string',
		'year'=>'string'
	);

	function __construct(){
		parent::__construct();
	}

	function setId($id){
		$this->id = $id;
	}
	
	function getId(){
		return $this->id;
	}

	

}

?>