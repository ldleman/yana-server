<?php

/*
 @nom: Dashboard
 @auteur: Valentin CARRUESCO (valentin.carruesco@sys1.fr)
 @description:  Classe de gestion des dashboard de widgets
 */

class Dashboard extends SQLiteEntity{

	public $id,$user,$label;
	protected $TABLE_NAME = 'plugin_dashboard_view';
	protected $CLASS_NAME = 'Dashboard';
	protected $object_fields = 
	array(
		'id'=>'key',
		'user'=>'id',
		'label'=>'longstring',
		'default' => 'int'
	);

	function __construct(){
		parent::__construct();
	}

}

?>