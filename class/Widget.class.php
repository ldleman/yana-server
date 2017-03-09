<?php

/*
 @nom: Widget
 @auteur: Valentin CARRUESCO (valentin.carruesco@sys1.fr)
 @description:  Classe de gestion des widget créés
 */

class Widget extends Entity{

	public $id,$minified,$position,$model,$dashboard,$title,$icon,$background,$width,$load,$move,$delete,$options,$js,$css,$content;
	protected $TABLE_NAME = 'widget';
	protected $fields = 
	array(
		'id'=>'key',
		'model'=>'longstring',
		'position'=>'int',
		'minified' => 'bool',
		'dashboard' => 'int'
	);

	function __construct(){
		parent::__construct();
		$this->options = array();
		$this->icon = 'fa-caret-right';
		$this->title = 'Sans titre';
		$this->width = 4;

	}

	public static function current(){
		global $_;
		$widget = new Widget();
		$widget->fromArray($_);
		return $widget;
	}

}



?>