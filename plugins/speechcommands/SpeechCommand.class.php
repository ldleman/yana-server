<?php

/**
* Classe de gestion SQL de la table SpeechCommand liée à la classe SpeechCommand 
* @author: valentin carruesco <idleman@idleman.fr>
*/


class SpeechCommand extends SQLiteEntity{

	public $id,$command,$action,$state,$confidence,$parameter;
	protected $TABLE_NAME = 'plugin_speechcommand'; 	
	protected $CLASS_NAME = 'SpeechCommand'; 
	protected $object_fields = 
	array( 
		'id'=>'key',
		'command'=>'longstring',
		'action'=>'string',
		'state'=>'int',
		'confidence'=>'string',
		'parameter'=>'longstring'
	
	);

	function __construct(){
		parent::__construct();
	}

}

?>