<?php

/**
 * Define SQL for Mysql database system
 * @author valentin carruesco
 * @category Core
 * @license copyright
 */

class Sqlite
{
	const label = 'SQLite3';
	const connection = 'sqlite:{{ROOT}}db/{{BASE_NAME}}.db';
	const description = 'Base légere monofichier sans authentification, simple d\'utilisation/installation mais limitée en performances';

	public static function fields(){
		return array(
			array('id'=>'name','label'=>'Nom de la base','default'=>'database','comment'=>'')
		);
	}
	
	public static function types(){
		$types = array();
        $types['string'] = $types['timestamp'] = $types['date'] = 'VARCHAR(255)';
        $types['longstring'] = 'TEXT';
        $types['key'] = 'INTEGER NOT NULL PRIMARY KEY';
        $types['object'] = $types['integer'] = 'bigint(20)';
        $types['boolean'] = 'INTEGER(1)';
        $types['blob'] = ' BLOB';
        $types['default'] = 'TEXT';
		return $types;
	}
	
	public static function select(){
		$sql = 'SELECT {{:selected}}{{value}}{{;}},{{/;}}{{/:selected}} FROM "{{table}}" {{?filter}}WHERE {{:filter}}"{{key}}"{{operator}}{{value}} {{;}} AND {{/;}} {{/:filter}} {{/?filter}}  {{?orderby}}ORDER BY {{:orderby}}{{value}}{{;}},{{/;}}{{/:orderby}} {{/?orderby}} {{?limit}}LIMIT {{:limit}}{{value}}{{;}},{{/;}}{{/:limit}}{{/?limit}}';
		return $sql;
	}
	public static function delete(){
		$sql = 'DELETE FROM {{table}} {{?filter}}WHERE {{:filter}}"{{key}}"{{operator}}{{value}} {{;}} AND {{/;}} {{/:filter}} {{/?filter}} {{?limit}}LIMIT {{:limit}}{{value}}{{;}},{{/;}}{{/:limit}}{{/?limit}}';
		return $sql;
	}
	public static function count(){
		$sql = 'SELECT COUNT({{selected}}) number FROM {{table}} {{?filter}}WHERE {{:filter}}"{{key}}"{{operator}}{{value}} {{;}} AND {{/;}} {{/:filter}} {{/?filter}}';
		return $sql;
	}
	public static function update(){
		$sql = 'UPDATE {{table}} SET {{?fields}} {{:fields}}"{{key}}"={{value}} {{;}}, {{/;}}{{/:fields}} {{/?fields}} {{?filters}}WHERE {{:filters}}{{key}}{{operator}}{{value}} {{;}} AND {{/;}} {{/:filters}} {{/?filters}}';
		return $sql;
	}
	public static function insert(){
		$sql = 'INSERT INTO  {{table}} ({{?fields}} {{:fields}}"{{key}}" {{;}} , {{/;}} {{/:fields}} {{/?fields}})VALUES({{?fields}} {{:fields}}{{value}} {{;}} , {{/;}} {{/:fields}} {{/?fields}})';
		return $sql;
	}
	public static function create(){
		$sql = 'CREATE TABLE IF NOT EXISTS "{{table}}" ({{?fields}} {{:fields}}"{{key}}" {{value}}{{;}} , {{/;}} {{/:fields}} {{/?fields}})';
		return $sql;
	}
	public static function drop(){
		$sql = 'DROP TABLE IF EXISTS "{{table}}";';
		return $sql;
	}
}

?>