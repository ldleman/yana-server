<?php

/*
 @nom: DoorAccess
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Gère les accès a la porte principale
 */

class DoorAccess extends SQLiteEntity{

	public $id,$code,$user,$limit,$require;
	protected $TABLE_NAME = 'plugin_door';
	protected $CLASS_NAME = 'DoorAccess';
	protected $object_fields = 
	array(
		'id'=>'key',
		'code'=>'string',
		'user'=>'int',
		'limit'=>'int',
		'require'=>'longstring'
	);

	function __construct(){
		parent::__construct();
		$this->limit = -1;
		$this->require = json_encode(array());
	}

	function setId($id){
		$this->id = $id;
	}
	
	function getId(){
		return $this->id;
	}

	

}

?>