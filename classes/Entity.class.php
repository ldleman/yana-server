<?php
require_once(dirname(__FILE__).'/../constant.php');

/**
	Classe mère de tous les modèles (classe entitées) liées a la base de donnée,
	cette classe est configuré pour agir avec une base SQLite, mais il est possible de redefinir ses codes SQL pour l'adapter à un autre SGBD sans affecter 
	le reste du code du projet.
	@author: idleman
	@version 2
**/


	class Entity
	{

		public $debug = false,$pdo = null;
		public static $lastError = '';
		public static $lastQuery = '';


		function __construct(){
			$this->connect();
		}

		function connect(){
			$this->pdo = Database::instance();
			if(!isset($this->TABLE_NAME)) $this->TABLE_NAME = strtolower(get_called_class());
		}

		public function __toString(){
			foreach($this->toArray() as $key=>$value){
				echo $key.' : '.$value.','.PHP_EOL;
			}
        	
    	}

		public static function debug(){
			return array(self::$lastQuery,self::$lastError);
		}

    	public function __sleep()
		{
		    return array_keys($this->toArray());
		}

		public function __wakeup()
		{
		    $this->connect();
		}

		function toArray(){
			$fields = array();
			foreach($this->fields as $field=>$type)
				$fields[$field]= $this->$field;
			return $fields;
		}
		function fromArray($array){
			foreach($array as $field=>$value)
				$this->$field = $value;
		}

		function sgbdType($type){
			$types = array();
			$types['string'] = $types['timestamp'] = $types['date'] = 'VARCHAR(255)';
			$types['longstring'] = 'TEXT';
			$types['key'] = 'INTEGER NOT NULL PRIMARY KEY';
			$types['object'] = $types['integer'] = 'bigint(20)';
			$types['boolean'] = 'INTEGER(1)';
			$types['blob'] = ' BLOB';
			$types['default'] = 'TEXT';
			return isset($types[$type]) ? $types[$type] : $types['default'];
		}


		public function closeDatabase(){
		//$this->close();
		}

		public static function tableName(){
			$class = get_called_class();
			$instance = new $class();
			return ENTITY_PREFIX.$instance->TABLE_NAME;
		}


		// GESTION SQL

		/**
		* Verifie l'existence de la table en base de donnée
		* @author Valentin CARRUESCO
		* @category manipulation SQL
		* @param <String> créé la table si elle n'existe pas
		* @return true si la table existe, false dans le cas contraire
		*/
		public static function checkTable($autocreate = false){
			$class = get_called_class();
			$instance = new $class();
			$query = 'SELECT count(*) as numRows FROM sqlite_master WHERE type="table" AND name=?';  
			$statement = $instance->customQuery($query,array($instance->tableName()));

			if($statement!=false){
				$statement = $statement->fetchArray();
				if($statement['numRows']==1){
					$return = true;
				}
			}
			if($autocreate && !$return) self::create();
			return $return;
		}
		
		public static function install($classDirectory){

			foreach(glob($classDirectory.DIRECTORY_SEPARATOR.'*.class.php') as $file){
				$infos = explode('.',basename($file));
				$class = array_shift($infos);
				if (!class_exists($class) || !method_exists ($class , 'create') || $class==get_class()) continue;
				$class::create();
			}
		}
	

		/**
		* Methode de creation de l'entité
		* @author Valentin CARRUESCO
		* @category manipulation SQL
		* @return Aucun retour
		*/
		public static function create(){
			$class = get_called_class();
			$instance = new $class();
			$query = 'CREATE TABLE IF NOT EXISTS `'.ENTITY_PREFIX.$instance->TABLE_NAME.'` (';

				foreach($instance->fields as $field=>$type)
					$query .='`'.$field.'`  '. $instance->sgbdType($type).' ,';

				$query = substr($query,0,strlen($query)-1);
				$query .= ');';
			$instance->customExecute($query);
		}

		public static function drop(){
			$class = get_called_class();
			$instance = new $class();
			$query = 'DROP TABLE `'.$instance->tableName().'`;';
			$instance->customExecute($query);
		}

		/**
		* Methode d'insertion ou de modifications d'elements de l'entité
		* @author Valentin CARRUESCO
		* @category manipulation SQL
		* @param  Aucun
		* @return Aucun retour
		*/
		public function save(){
			$data = array();
			if(isset($this->id) && $this->id>0){
				$query = 'UPDATE `'.ENTITY_PREFIX.$this->TABLE_NAME.'` SET ';
				foreach($this->fields as $field=>$type){
					$value = $this->{$field};
					$query .= '`'.$field.'`=?,';
					$data[] = $value;
				}
				$query = substr($query,0,strlen($query)-1);
				$data[] = $this->id;
				$query .= ' WHERE `id`=?;';
			}else{
				$query = 'INSERT INTO `'.$this->tableName().'`(';
					foreach($this->fields as $field=>$type){
						if($type!='key')
							$query .='`'.$field.'`,';
					}
					$query = substr($query,0,strlen($query)-1);
					$query .=')VALUES(';
					
					foreach($this->fields as $field=>$type){
						if($type=='key') continue;
						$query .='?,';
						$data[] = $this->{$field};
					}
					$query = substr($query,0,strlen($query)-1);

					$query .=');';
				}
				$this->customExecute($query,$data);
				
				$this->id =  (!isset($this->id) || !(is_numeric($this->id))?$this->pdo->lastInsertId():$this->id);
		}

		/**
		* Méthode de modification d'éléments de l'entité
		* @author Valentin CARRUESCO
		* @category manipulation SQL
		* @param <Array> $colonnes=>$valeurs
		* @param <Array> $colonnes (WHERE) =>$valeurs (WHERE)
		* @param <String> $operation="=" definis le type d'operateur pour la requete select
		* @return Aucun retour
		*/
		public static function change($columns,$columns2=null,$operation='='){
			$class = get_called_class();
			$instance = new $class();
			$data = array();
			$query = 'UPDATE `'.$instance->tableName().'` SET ';

			foreach ($columns as $column=>$value){
				$query .= '`'.$column.'`=?,';
				$data[] = $value;
			}
			$query = substr($query,0,strlen($query)-1);
			if($columns2!=null){
				$query .=' WHERE '; 
				
				foreach ($columns2 as $column=>$value){
					$query .= '`'.$column.'`'.$operation.'?,';
					$data[] = $value;
				}
				$query = substr($query,0,strlen($query)-1);
			}
			$instance->customExecute($query,$data);
		}

		/**
		* Méthode de selection de tous les elements de l'entité
		* @author Valentin CARRUESCO
		* @category manipulation SQL
		* @param <String> $ordre=null
		* @param <String> $limite=null
		* @return <Array<Entity>> $Entity
		*/
		public static function populate($order=null,$limit=null){
			$results = self::loadAll(array(),$order,$limit,'=');
			return $results;
		}


		/**
		* Méthode de selection multiple d'elements de l'entité
		* @author Valentin CARRUESCO
		* @category manipulation SQL
		* @param <Array> $colonnes (WHERE)
		* @param <Array> $valeurs (WHERE)
		* @param <String> $ordre=null
		* @param <String> $limite=null
		* @param <String> $operation="=" definis le type d'operateur pour la requete select
		* @return <Array<Entity>> $Entity
		*/
		public static function loadAll($columns=array(),$order=null,$limit=null,$operation="=",$selColumn='*'){
			$objects = array();
			$whereClause = '';
			$data = array();
			if($columns!=null && sizeof($columns)!=0){
				$whereClause .= ' WHERE ';
				$i = false;
				foreach($columns as $column=>$value){
					if($i){$whereClause .=' AND ';}else{$i=true;}
					$whereClause .= '`'.$column.'`'.$operation.'?';
					$data[] = $value;
				}
			}

			$class = get_called_class();
			$instance = new $class();
			$query = 'SELECT '.$selColumn.' FROM `'.$instance->tableName().'` '.$whereClause.' ';
			
			if($order!=null) $query .='ORDER BY '.$order.' ';
			if($limit!=null) $query .='LIMIT '.$limit.' ';
			$query .=';';
			return $instance->customQuery($query,$data,true);
		}

		public static function loadAllOnlyColumn($selColumn,$columns,$order=null,$limit=null,$operation="="){
			$objects = self::loadAll($columns,$order,$limit,$operation,$selColumn);
			if(count($objects)==0)$objects = array();
			return $objects;
		}
		


		/**
		* Méthode de selection unique d'élements de l'entité
		* @author Valentin CARRUESCO
		* @category manipulation SQL
		* @param <Array> $colonnes (WHERE)
		* @param <Array> $valeurs (WHERE)
		* @param <String> $operation="=" definis le type d'operateur pour la requete select
		* @return <Entity> $Entity ou false si aucun objet n'est trouvé en base
		*/
		public static function load($columns,$operation='='){
			$objects = self::loadAll($columns,null,'1',$operation);
			if(!isset($objects[0]))$objects[0] = false;
			return $objects[0];
		}

		/**
		* Méthode de selection unique d'élements de l'entité
		* @author Valentin CARRUESCO
		* @category manipulation SQL
		* @param <Array> $colonnes (WHERE)
		* @param <Array> $valeurs (WHERE)
		* @param <String> $operation="=" definis le type d'operateur pour la requete select
		* @return <Entity> $Entity ou false si aucun objet n'est trouvé en base
		*/
		public static function getById($id,$operation='='){
			return self::load(array('id'=>$id),$operation);
		}

		/**
		* Methode de comptage des éléments de l'entité
		* @author Valentin CARRUESCO
		* @category manipulation SQL
		* @return<Integer> nombre de ligne dans l'entité'
		*/
		public static function rowCount($columns=null)
		{
			$class = get_called_class();
			$instance = new $class();
			$whereClause ='';
			$data = array();
			if($columns!=null){
				$whereClause = ' WHERE ';
				$i=false;
				foreach($columns as $column=>$value){
					if($i){$whereClause .=' AND ';}else{$i=true;}
					$whereClause .= '`'.$column.'`=?';
					$data[] = $value;
				}
			}
			$query = 'SELECT COUNT(id) resultNumber FROM '.$instance->tableName().$whereClause;
			
			$execQuery = $instance->customQuery($query,$data);
			$row = $execQuery->fetch();

			return $row['resultNumber'];
		}	

		/**
		* Methode de définition de l'éxistence d'un moins un des éléments spécifiés en base
		* @author Valentin CARRUESCO
		* @category manipulation SQL
		* @return<boolean> existe (true) ou non (false)
		*/
		public static function exist($columns=null)
		{
			$result = self::rowCount($columns);

			return ($result!=0);
		}	

		public static function deleteById($id){
			
			self::delete(array('id'=>$id));
		}
		/**
		* Méthode de supression d'elements de l'entité
		* @author Valentin CARRUESCO
		* @category manipulation SQL
		* @param <Array> $colonnes (WHERE)
		* @param <Array> $valeurs (WHERE)
		* @param <String> $operation="=" definis le type d'operateur pour la requete select
		* @return Aucun retour
		*/
		public static function delete($columns,$operation='=',$limit=null){
			
			$class = get_called_class();
			$instance = new $class();
			$whereClause = '';
			$i=false;
			$data = array();
			foreach($columns as $column=>$value){
				if($i){$whereClause .=' AND ';}else{$i=true;}
				$whereClause .= '`'.$column.'`'.$operation.'?';
				$data[]=$value; 
			}
			$query = 'DELETE FROM `'.ENTITY_PREFIX.$instance->TABLE_NAME.'` WHERE '.$whereClause.' '.(isset($limit)?'LIMIT '.$limit:'').';';
			$instance->customExecute($query,$data);
		}
		
		public function customExecute($query,$data = array()){
			self::$lastQuery = $query;
			$stm = $this->pdo->prepare($query);
			try{
				$stm->execute($data);
			}catch(Exception $e){
				self::$lastError = $this->pdo->errorInfo();
				throw new Exception($e->getMessage());
			}
			
		}

		public static function staticQuery($query,$data = array(),$fill = false){
			$class = get_called_class();
			$instance = new $class();
			return $instance->customQuery($query,$data,$fill);
		}

		public function customQuery($query,$data = array(),$fill = false){
			self::$lastQuery = $query;
			
			$results = $this->pdo->prepare($query);
			
			$results->execute($data);
			
			if(!$results){
				self::$lastError = $this->pdo->errorInfo();
				return false;
			}else{

				if(!$fill)	return $results;

				$class = get_class($this);
				$objects = array();
				while($queryReturn = $results->fetch() ){
					$object = new $class();
					foreach($this->fields as $field=>$type){
						if(isset($queryReturn[$field]))
							$object->{$field} =  $queryReturn[$field];
					}
					$objects[] = $object;
					unset($object);
				}
				
				return $objects == null?array()  : $objects;
			}
		}


		public function __get($name)
		{
				$pos = strpos($name,'_object');
				if($pos!==false){
					$field = strtolower(substr($name,0,$pos));
					if(array_key_exists($field,$this->fields)){
						$class = ucfirst($field);
						return $class::getById($this->{$field});
					}
				}
				throw new Exception("Attribut ".get_class($this)."->$name non existant");
		}
	}
?>
