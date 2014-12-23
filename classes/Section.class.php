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


	public static function add($name,$description="",$grantAdmin = true){
		$sectionManager = new Section();
		if($sectionManager->rowCount(array('label'=>$name))==0){
			$sectionManager->setLabel($name);
			$sectionManager->setDescription($description);
			$sectionManager->save();

			if($grantAdmin){
				$right = new Right();
				$right = $right->load(array('section'=>$sectionManager->getLabel(),'rank'=>1));
				$right = (!$right?new Right():$right);
				$right->setSection($sectionManager->getId());
				$right->setCreate(1);
				$right->setRead(1);
				$right->setUpdate(1);
				$right->setDelete(1);
				$right->setRank(1);
				$right->save();
			}
		}
	}

	public static function remove($name){
		$sectionManager = new Section();
		$sectionManager->load(array('label'=>$name));
		$rightManager = new Right();
		$rightManager->delete(array('section'=>$sectionManager->getId()));
		$sectionManager->delete(array('id'=>$sectionManager->getId()));
	}


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