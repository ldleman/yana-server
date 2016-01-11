<?php

/*
 @nom: Camera
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Classe de gestion des camera IP
 */

class Camera extends SQLiteEntity{

	public $id,$location,$label,$ip,$login,$password;
	protected $TABLE_NAME = 'plugin_ipcam_camera';
	protected $CLASS_NAME = 'Camera';
	protected $object_fields = 
	array(
		'id'=>'key',
		'label'=>'string',
		'ip'=>'string',
		'login'=>'string',
		'password'=>'string',
		'location'=>'string'
	);

	function __construct($tag){
		parent::__construct($tag);
	}


	

}

?>