<?php

/*
 @nom: Room
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Classe de gestion des pieces
 */

class Room extends Entity{

	public $id,$label,$description,$state;
	protected $TABLE_NAME = 'room';

	protected $fields = 
	array(
		'id'=>'key',
		'label'=>'string',
		'description'=>'string',
		'state' => 'int'
	);

	function __construct(){
		$this->state = 0;
		parent::__construct();
	}


}

?>