<?php

/*
 @nom: Room
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Classe de gestion des pieces
 */

class Room extends SQLiteEntity{

	public $id,$name,$description,$state;
	protected $TABLE_NAME = 'plugin_room';
	protected $CLASS_NAME = 'Room';
	protected $object_fields = 
	array(
		'id'=>'key',
		'name'=>'string',
		'description'=>'string',
		'state' => 'int'
	);

	function __construct(){
		$this->state = 0;
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

}

?>