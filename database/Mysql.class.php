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
	const fields = 'host,database,login,password';
	
	public static function types(){
		$types = array();
		$types['string'] = $types['timestamp'] = $types['date'] = 'VARCHAR(225) CHARACTER SET utf8 COLLATE utf8_general_ci';
		$types['longstring'] = 'TEXT CHARACTER SET utf8 COLLATE utf8_general_ci';
		$types['key'] = 'int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY';
		$types['object'] = $types['integer'] = 'INT(11)';
		$types['boolean'] = 'INT(1)';
		$types['blob'] = ' BLOB';
		$types['default'] = 'TEXT CHARACTER SET utf8 COLLATE utf8_general_ci';
		return $types;
	}
	
	public static function select(){
		$sql = 'SELECT {{:selected}}{{value}}{{;}},{{/;}}{{/:selected}} FROM `{{table}}` {{?filter}}WHERE {{:filter}}`{{key}}`{{operator}}{{value}} {{;}} AND {{/;}} {{/:filter}} {{/?filter}} {{?limit}}LIMIT {{:limit}}{{value}}{{;}},{{/;}}{{/:limit}}{{/?filter}} {{?orderby}}ORDER BY {{:orderby}}{{value}}{{;}},{{/;}}{{/:orderby}} {{/?orderby}}';
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
		$sql = 'UPDATE {{table}} SET {{?fields}} {{:fields}}{{key}}={{value}} {{;}} , {{/;}} {{/:fields}} {{/?fields}} {{?filters}}WHERE {{:filters}}{{key}}{{operator}}{{value}} {{;}} AND {{/;}} {{/:filters}} {{/?filters}}';
		return $sql;
	}
	public static function insert(){
		$sql = 'INSERT INTO  {{table}} ({{?fields}} {{:fields}}{{key}} {{;}} , {{/;}} {{/:fields}} {{/?fields}})VALUES({{?fields}} {{:fields}}{{value}} {{;}} , {{/;}} {{/:fields}} {{/?fields}})';
		return $sql;
	}
	public static function create(){
		$sql = 'CREATE TABLE IF NOT EXISTS `{{table}}` ({{?fields}} {{:fields}}`{{key}}` {{value}}{{;}} , {{/;}} {{/:fields}} {{/?fields}})';
		return $sql;
	}
	public static function drop(){
		$sql = 'DROP TABLE IF EXISTS `{{table}}`;';
		return $sql;
	}
}

?>