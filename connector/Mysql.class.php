<?php

/**
 * Define SQL for Mysql database system
 * @author valentin carruesco
 * @category Core
 * @license copyright
 */

class Mysql
{
	const label = 'MySQL';
	const connection = 'mysql:host={{BASE_HOST}};dbname={{BASE_NAME}}';
	const description = 'Base robuste authentifiée necessitant un serveur Mysql (Conseillé)';

	public static function fields(){
		return array(
			array('id'=>'host','label'=>'Serveur','default'=>'localhost','comment'=>''),
			array('id'=>'login','label'=>'Identifiant','default'=>'','comment'=>''),
			array('id'=>'password','label'=>'Mot de passe','default'=>'','comment'=>''),
			array('id'=>'name','label'=>'Nom de la base','default'=>'','comment'=>'')
		);
	}

	public static function types(){
		$types = array();
		$types['string'] = $types['timestamp'] = $types['date'] = 'VARCHAR(225) CHARACTER SET utf8 COLLATE utf8_general_ci';
		$types['longstring'] = 'TEXT CHARACTER SET utf8 COLLATE utf8_general_ci';
		$types['key'] = 'int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY';
		$types['object'] = $types['int'] = 'INT(11)';
		$types['boolean'] = 'tinyint(1) NOT NULL DEFAULT \'0\'';
		$types['blob'] = ' BLOB';
		$types['default'] = 'TEXT CHARACTER SET utf8 COLLATE utf8_general_ci';
		return $types;
	}
	
	public static function select(){
		$sql = 'SELECT {{:selected}}{{value}}{{;}},{{/;}}{{/:selected}} FROM `{{table}}` {{?filter}} WHERE {{:filter}} `{{key}}` {{operator}} {{value}} {{;}} AND {{/;}} {{/:filter}} {{/?filter}} {{?orderby}}ORDER BY {{:orderby}}{{value}}{{;}},{{/;}}{{/:orderby}} {{/?orderby}} {{?limit}}LIMIT {{:limit}}{{value}}{{;}},{{/;}}{{/:limit}}{{/?limit}}';
		return $sql;
	}
	public static function delete(){
		$sql = 'DELETE FROM {{table}} {{?filter}}WHERE {{:filter}}{{key}}{{operator}}{{value}} {{;}} AND {{/;}} {{/:filter}} {{/?filter}} {{?limit}}LIMIT {{:limit}}{{value}}{{;}},{{/;}}{{/:limit}}{{/?limit}}';
		return $sql;
	}
	public static function count(){
		$sql = 'SELECT COUNT({{selected}}) number FROM {{table}} {{?filter}}WHERE {{:filter}}{{key}}{{operator}}{{value}} {{;}} AND {{/;}} {{/:filter}} {{/?filter}}';
		return $sql;
	}
	public static function update(){
		$sql = 'UPDATE {{table}} SET {{?fields}} {{:fields}}`{{key}}`={{value}} {{;}} , {{/;}} {{/:fields}} {{/?fields}} {{?filters}}WHERE {{:filters}}{{key}}{{operator}}{{value}} {{;}} AND {{/;}} {{/:filters}} {{/?filters}}';
		return $sql;
	}
	public static function insert(){
		$sql = 'INSERT INTO  {{table}} ({{?fields}} {{:fields}}`{{key}}` {{;}} , {{/;}} {{/:fields}} {{/?fields}})VALUES({{?fields}} {{:fields}}{{value}} {{;}} , {{/;}} {{/:fields}} {{/?fields}})';
		return $sql;
	}
	public static function create(){
		$sql = 'CREATE TABLE IF NOT EXISTS `{{table}}` ({{?fields}} {{:fields}}`{{key}}` {{value}}{{;}} , {{/;}} {{/:fields}} {{/?fields}}) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
		return $sql;
	}
	public static function drop(){
		$sql = 'DROP TABLE IF EXISTS `{{table}}`;';
		return $sql;
	}
}

?>