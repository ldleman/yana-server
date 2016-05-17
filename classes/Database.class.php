<?php

/**
* PDO Connector for database connexion.
* @author v.carruesco
* @category Core
* @license copyright
*/
class Database
{
	public $connection = null;
	public static $instance = null;
	private function __construct(){
		$this->connect();
	}

	/**
	* Methode de recuperation unique de l'instance
	* @author Valentin CARRUESCO
	* @category Singleton
	* @param <Aucun>
	* @return <pdo> $instance
	*/
	public static function instance(){
		if (Database::$instance === null) {
			Database::$instance = new self(); 
		}
		return Database::$instance->connection;
	}
	
	public function connect(){
		try {
			$this->connection = new PDO(BASE_CONNECTION_STRING, BASE_LOGIN, BASE_PASSWORD);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
		} catch ( Exception $e ) {
		  echo "Connection à la base impossible : ", $e->getMessage();
		  die();
		}
	}
}
?>