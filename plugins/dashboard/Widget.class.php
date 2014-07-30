<?php

/*
 @nom: Widget
 @auteur: Valentin CARRUESCO (valentin.carruesco@sys1.fr)
 @description:  Classe de gestion des widget créés
 */

class Widget extends SQLiteEntity{

	public $id,$minified,$column,$cell,$model,$data;
	protected $TABLE_NAME = 'plugin_dashboard';
	protected $CLASS_NAME = 'Widget';
	protected $object_fields = 
	array(
		'id'=>'key',
		'model'=>'longstring',
		'data'=>'longstring',
		'cell'=>'int',
		'column'=>'int',
		'minified' => 'int'
	);

	function __construct(){
		parent::__construct();
	}

}

?>