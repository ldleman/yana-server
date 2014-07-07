<?php

/*
 @nom: Cause
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Représente une cause de scénario (combinées à d'autres causes ou seule, conduit à un ou plusieurs effets)
 */

class Cause extends SQLiteEntity{

	public $id,$story,$sort,$type,$value,$target,$operator,$union;
	protected $TABLE_NAME = 'plugin_story_cause';
	protected $CLASS_NAME = 'Cause';
	protected $object_fields = 
	array(
		'id'=>'key',
		'story'=>'int',
		'sort'=>'int',
		'type'=>'string',
		'value'=>'longstring',
		'target'=>'longstring',
		'union'=>'string',
		'operator'=>'string'
	);

	function __construct(){
		parent::__construct();
	}
}

?>