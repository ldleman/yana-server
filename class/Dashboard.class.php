<?php

/*
 @nom: Dashboard
 @auteur: Valentin CARRUESCO (valentin.carruesco@sys1.fr)
 @description:  Classe de gestion des dashboard de widgets
 */

 class Dashboard extends Entity{

 	public $id,$user,$label,$icon,$default;
 	protected $TABLE_NAME = 'dashboard';
 	protected $fields = 
 	array(
 		'id'=>'key',
 		'user'=>'id',
 		'label'=>'longstring',
 		'icon'=>'string',
 		'default' => 'bool'
 		);

 	function __construct(){
 		parent::__construct();
 	}
 }

 ?>