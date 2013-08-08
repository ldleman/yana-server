<?php

/*
 @nom: Door
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Classe de gestion des relais filaires
 */

class Door extends SQLiteEntity{

	protected $id,$name,$description,$pinRelay,$pinCaptor,$room;
	protected $TABLE_NAME = 'plugin_door';
	protected $CLASS_NAME = 'Door';
	protected $object_fields = 
	array(
		'id'=>'key',
		'name'=>'string',
		'description'=>'string',
		'pinRelay'=>'int',
		'pinCaptor'=>'int',
		'room'=>'int'
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

	function getName(){
		return $this->name;
	}

	function setName($name){
		$this->name = $name;
	}

	function getDescription(){
		return $this->description;
	}

	function setDescription($description){
		$this->description = $description;
	}

	function getPinRelay(){
		return $this->pinRelay;
	}

	function setPinRelay($pinRelay){
		$this->pinRelay = $pinRelay;
	}

	function getPinCaptor(){
		return $this->pinCaptor;
	}

	function setPinCaptor($pinCaptor){
		$this->pinCaptor = $pinCaptor;
	}

	function getRoom(){
		return $this->room;
	}

	function setRoom($room){
		$this->room = $room;
	}

}

?>