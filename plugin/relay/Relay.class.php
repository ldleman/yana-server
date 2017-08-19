<?php

/*
 @nom: Relay
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Classe de gestion des relais (prises, interrupteurs, coupe circuits ...)
 */

class Relay extends Entity{
	public $id,$location,$label,$type,$description,$icon,$meta;
	protected $TABLE_NAME = 'plugin_relay';
	protected $fields = 
	array(
		'id'=>'key',
		'label'=>'string',
		'type'=>'string',
		'description'=>'longstring',
		'meta'=>'longstring',
		'icon'=>'string',
		'location'=>'int'
	);



	function __construct(){
		parent::__construct();
	}

	public static function types($key=null){
		$types = array();
		Plugin::callhook("relay_types", array(&$types));
		if(isset($key)) return isset($types[$key]) ? $types[$key]: '';
		return $types;
	}

	static function availableIcon($key=null){
		
		$icons = array (
				'fa-lightbulb-o'=>array('#FFED00','#ffdc00'),
				'fa-power-off'=>array('#BDFF00','#4fff00'),
				'fa-flash'=>array('#FFFFFF','#00FFD9'),
				'fa-gears'=>array('#FFFFFF','#FF00E4'),
				'fa-align-justify'=>array('#ffffff','#ffffff'),
				'fa-adjust'=>array('#ffffff','#ffffff'),
				'fa-arrow-circle-o-right'=>array('#ffffff','#ffffff'),
				'fa-desktop'=>array('#ffffff','#ffffff'),
				'fa-music'=>array('#ffffff','#ffffff'),
				'fa-bell-o'=>array('#ffffff','#ffffff'),
				'fa-beer'=>array('#ffffff','#ffffff'),
				'fa-bullseye'=>array('#ffffff','#ffffff'),
				'fa-automobile'=>array('#ffffff','#ffffff'),
				'fa-book'=>array('#ffffff','#ffffff'),
				'fa-bomb'=>array('#ffffff','#ffffff'),
				'fa-clock-o'=>array('#ffffff','#ffffff'),
				'fa-cutlery'=>array('#ffffff','#ffffff'),
				'fa-microphone'=>array('#ffffff','#ffffff'),
				'fa-anchor'=>array('#ffffff','#ffffff'),
				'fa-bed'=>array('#ffffff','#ffffff'),
				'fa-briefcase'=>array('#ffffff','#ffffff'),
				'fa-bus'=>array('#ffffff','#ffffff'),
				'fa-commenting-o'=>array('#ffffff','#ffffff'),
				'fa-eye'=>array('#ffffff','#ffffff'),
				'fa-female'=>array('#ffffff','#ffffff'),
				'fa-male'=>array('#ffffff','#ffffff'),
				'fa-fire-extinguisher'=>array('#ffffff','#ffffff'),
				'fa-group'=>array('#ffffff','#ffffff'),
				'fa-hand-pointer-o'=>array('#ffffff','#ffffff'),
				'fa-hand-stop-o'=>array('#ffffff','#ffffff'),
				'fa-hand-spock-o'=>array('#ffffff','#ffffff'),
				'fa-hand-peace-o'=>array('#ffffff','#ffffff'),

				'fa-tint'=>array('#ffffff','#ffffff')		
				);
		return isset($key) && isset($icons[$key]) ? $icons[$key] : $icons;
	}

}

?>