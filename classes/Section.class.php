<?php

/*
 @nom: Section
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Classe de gestion des sections
 */

class Section extends SQLiteEntity{

	protected $id,$label,$description;
	protected $TABLE_NAME = 'section';
	protected $CLASS_NAME = 'Section';
	protected $object_fields = 
	array(
		'id'=>'key',
		'label'=>'string',
		'description'=>'longstring'
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

	function getLabel(){
		return $this->label;
	}

	function setLabel($label){
		$this->label = $label;
	}

	function getDescription(){
		return $this->description;
	}

	function setDescription($description){
		$this->description = $description;
	}
}

?>