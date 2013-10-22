<?php

/*
 @nom: WireRelay
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Classe de gestion des relais filaires
 */

class WireRelay extends SQLiteEntity{

	protected $id,$name,$description,$pin,$room,$pulse;
	protected $TABLE_NAME = 'plugin_wireRelay';
	protected $CLASS_NAME = 'WireRelay';
	protected $object_fields = 
	array(
		'id'=>'key',
		'name'=>'string',
		'description'=>'string',
		'pin'=>'int',
		'room'=>'int',
		'pulse'=>'int'
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

	function getPin(){
		return $this->pin;
	}

	function setPin($pin){
		$this->pin = $pin;
	}

	function getRoom(){
		return $this->room;
	}

	function setRoom($room){
		$this->room = $room;
	}

	function getPulse(){
		return $this->pulse;
	}

	function setPulse($pulse){
		$this->pulse = $pulse;
	}

}

?>