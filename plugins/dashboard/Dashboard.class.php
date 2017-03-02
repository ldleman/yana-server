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

	public static function initForUser($id){

		foreach(array('Salon','Cuisine','Chambre','Garage','Système') as $room){
			$entity = new Dashboard();
			$entity->create();
			$entity->user = $id;
			$entity->label = $room;
			$entity->default = 0;
			$entity->save();
		}
		$entity = new Dashboard();
		$entity->create();
		$entity->user = $id;
		$entity->label = "Général";
		$entity->default = 1;
		$entity->save();



		$dashboard = $entity->id;

		$entity = new Widget();
		$entity->create();

		$widgets = array(
			'dash_profil'=>array('cell'=>0,'column'=>0),
			'dash_monitoring_ram'=>array('cell'=>0,'column'=>1),
			'dash_monitoring_system'=>array('cell'=>1,'column'=>1),
			'dash_monitoring_network'=>array('cell'=>2,'column'=>1),
			'dash_monitoring_services'=>array('cell'=>3,'column'=>1),
			'dash_monitoring_hdd'=>array('cell'=>4,'column'=>1),
			'dash_monitoring_disk'=>array('cell'=>5,'column'=>1),
			'dash_monitoring_gpio'=>array('cell'=>1,'column'=>0),
			'dash_monitoring_users'=>array('cell'=>0,'column'=>0),
			'dash_monitoring_vocal'=>array('cell'=>5,'column'=>2),
			'dash_monitoring_logs'=>array('cell'=>6,'column'=>2)
		);
		foreach($widgets as $widget=>$position):
			$entity->customQuery('INSERT INTO "yana_plugin_dashboard" ("model", "data", "cell", "column", "minified","dashboard") VALUES (\''.$widget.'\',	\'null\',	\''.$position['cell'].'\',	\''.$position['column'].'\',	\'\',\''.$dashboard.'\');');
		endforeach;
		
		return $dashboard;
	}

}

?>