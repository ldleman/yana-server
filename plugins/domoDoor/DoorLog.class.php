<?php

/*
 @nom: DoorLog
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Gère l'historique des accès a la porte principale
 */

class DoorLog extends SQLiteEntity{

	public $id,$user,$code,$time,$success;
	protected $TABLE_NAME = 'plugin_door_log';
	protected $CLASS_NAME = 'DoorLog';
	protected $object_fields = 
	array(
		'id'=>'key',
		'code'=>'string',
		'user'=>'int',
		'time'=>'string',
		'success'=>'string'
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