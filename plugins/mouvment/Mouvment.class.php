<?php

/*
 @nom: Mouvment
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Classe de gestion des peices
 */

class Mouvment extends SQLiteEntity{

	protected $id,$name,$description,$pin,$state,$room,$lastState;
	protected $TABLE_NAME = 'plugin_mouvment';
	protected $CLASS_NAME = 'Mouvment';
	protected $object_fields = 
	array(
		'id'=>'key',
		'name'=>'string',
		'description'=>'string',
		'pin'=>'int',
		'state'=>'int',
		'lastState'=>'string',
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

	function getPin(){
		return $this->pin;
	}

	function setPin($pin){
		$this->pin = $pin;
	}

	function getState(){
		return $this->state;
	}

	function setState($state){
		$this->state = $state;
	}


	function getRoom(){
		return $this->room;
	}

	function setRoom($room){
		$this->room = $room;
	}

	function setLastState($lastState){
		$this->lastState = $lastState;
	}
	function getLastState(){
		return $this->lastState;
	}

}

?>