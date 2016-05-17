<?php

/*
 @nom: Camera
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Classe de gestion des camera IP
 */

class Camera extends SQLiteEntity{

	public $id,$location,$label,$ip,$login,$password,$pattern;
	protected $TABLE_NAME = 'plugin_ipcam_camera';
	protected $CLASS_NAME = 'Camera';
	protected $object_fields = 
	array(
		'id'=>'key',
		'label'=>'string',
		'ip'=>'string',
		'login'=>'string',
		'password'=>'string',
		'location'=>'string',
		'pattern'=>'string'
	);

	function __construct($tag='rw'){
		parent::__construct($tag);
	}

	//Ajoutez votre modèle de caméra et son url d'appel vidéo ici
	//N'hésitez pas a partager votre ajout sur https://github.com/ldleman/yana-server/issues/new afin d'en faire profiter la communauté
	public static function brands(){
		return array(
		"Scricam" => 'http://{{login}}:{{password}}@{{ip}}/videostream.cgi',
		"Foscam fi8908" => 'http://{{ip}}/videostream.cgi?user={{login}}&pwd={{password}}&resolution=32&rate=0',
		"Autre" => 'http://{{login}}:{{password}}@{{ip}}');
	}
	

}

?>