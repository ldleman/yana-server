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


		$entity->customQuery('INSERT INTO "yana_plugin_dashboard" ("model", "data", "cell", "column", "minified","dashboard") VALUES (\'dash_profil\',	\'null\',	\'0\',	\'0\',	\'\',\''.$dashboard.'\');');
		$entity->customQuery('INSERT INTO "yana_plugin_dashboard" ("model", "data", "cell", "column", "minified","dashboard") VALUES (\'dash_monitoring_ram\',	\'null\',	\'0\',	\'1\',	\'\',\''.$dashboard.'\');');
		$entity->customQuery('INSERT INTO "yana_plugin_dashboard" ("model", "data", "cell", "column", "minified","dashboard") VALUES (\'dash_monitoring_system\',	\'null\',	\'0\',	\'2\',	\'\',\''.$dashboard.'\');');
		$entity->customQuery('INSERT INTO "yana_plugin_dashboard" ("model", "data", "cell", "column", "minified","dashboard") VALUES (\'dash_monitoring_network\',	\'null\',	\'0\',	\'2\',	\'\',\''.$dashboard.'\');');
		$entity->customQuery('INSERT INTO "yana_plugin_dashboard" ("model", "data", "cell", "column", "minified","dashboard") VALUES (\'dash_monitoring_hdd\',	\'null\',	\'3\',	\'1\',	\'\',\''.$dashboard.'\');');
		$entity->customQuery('INSERT INTO "yana_plugin_dashboard" ("model", "data", "cell", "column", "minified","dashboard") VALUES (\'dash_monitoring_disk\',	\'null\',	\'0\',	\'1\',	\'\',\''.$dashboard.'\');');
		$entity->customQuery('INSERT INTO "yana_plugin_dashboard" ("model", "data", "cell", "column", "minified","dashboard") VALUES (\'dash_monitoring_gpio\',	\'null\',	\'1\',	\'0\',	\'\',\''.$dashboard.'\');');
		$entity->customQuery('INSERT INTO "yana_plugin_dashboard" ("model", "data", "cell", "column", "minified","dashboard") VALUES (\'dash_monitoring_services\',	\'null\',	\'0\',	\'2\',	\'\',\''.$dashboard.'\');');
		$entity->customQuery('INSERT INTO "yana_plugin_dashboard" ("model", "data", "cell", "column", "minified","dashboard") VALUES (\'dash_monitoring_users\',	\'null\',	\'0\',	\'0\',	\'\',\''.$dashboard.'\');');
		$entity->customQuery('INSERT INTO "yana_plugin_dashboard" ("model", "data", "cell", "column", "minified","dashboard") VALUES (\'dash_monitoring_vocal\',	\'null\',	\'1\',	\'2\',	\'\',\''.$dashboard.'\');');
		$entity->customQuery('INSERT INTO "yana_plugin_dashboard" ("model", "data", "cell", "column", "minified","dashboard") VALUES (\'dash_monitoring_logs\',	\'null\',	\'1\',	\'2\',	\'\',\''.$dashboard.'\');');
	
		return $dashboard;
	}

}

?>