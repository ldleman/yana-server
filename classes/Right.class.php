<?php

/*
 @nom: Right
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Classe de gestion des utilisateurs
 */

class Right extends SQLiteEntity{

	protected $id,$label,$description;
	protected $TABLE_NAME = 'right';
	protected $CLASS_NAME = 'Right';
	protected $object_fields = 
	array(
		'id'=>'key',
		'rank'=>'int',
		'section'=>'string',
		'read'=>'boolean',
		'delete'=>'boolean',
		'create'=>'boolean',
		'update'=>'boolean'
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

	function getRank(){
		return $this->rank;
	}

	function setRank($rank){
		$this->rank = $rank;
	}

	function getSection(){
		return $this->section;
	}

	function setSection($section){
		$this->section = $section;
	}

	function getRead(){
		return $this->read;
	}

	function setRead($read){
		$this->read = $read;
	}

	function getCreate(){
		return $this->create;
	}

	function setCreate($create){
		$this->create = $create;
	}

	function getDelete(){
		return $this->delete;
	}

	function setDelete($delete){
		$this->delete = $delete;
	}

	function getUpdate(){
		return $this->update;
	}

	function setUpdate($update){
		$this->update = $update;
	}
}

?>