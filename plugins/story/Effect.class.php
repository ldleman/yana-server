<?php

/*
 @nom: Effect
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Représente un effet de scénario (une conséquence produite par une ou plusieurs causes)
 */

class Effect extends SQLiteEntity{

	public $id,$story,$sort,$type,$value,$target,$operator,$union;
	protected $TABLE_NAME = 'plugin_story_effect';
	protected $CLASS_NAME = 'Effect';
	protected $object_fields = 
	array(
		'id'=>'key',
		'story'=>'int',
		'sort'=>'int',
		'type'=>'string',
		'union'=>'string',
		'value'=>'longstring',
		'target'=>'longstring'
	);

	function __construct(){
		parent::__construct();
	}
}

?>