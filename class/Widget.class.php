<?php

/*
 @nom: Widget
 @auteur: Valentin CARRUESCO (valentin.carruesco@sys1.fr)
 @description:  Classe de gestion des widget créés
 */


class Widget extends Entity{

	public $id,$minified,$position,$model,$dashboard,$title,$icon,$background,$width,$load,$configure,$move,$delete,$options,$js,$css,$content,$data,$description;
	protected $TABLE_NAME = 'widget';
	protected $fields = 
	array(
		'id'=>'key',
		'model'=>'longstring',
		'data'=>'longstring',
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

	function data($key=null,$value=null){
		$data = json_decode($this->data,true);
		if($key==null) return $data;
		if(is_array($key) && $value==null){
			foreach ($key as $k => $v) {
				$data[$k] = $v;
				$this->data = json_encode($data);
			}
			return true;
		}
		if($value==null) return isset($data[$key])?$data[$key]:'';
		$data[$key] = $value;
		$this->data = json_encode($data);
		return true;
	}

	public static function current(){
		global $_;
		$widget = new Widget();
		$widget->fromArray($_);
		$widget->data = Widget::getById($widget->id)->data;
		return $widget;
	}

}



?>