<?php

require_once __DIR__.'/../constant.php';
require_once(__ROOT__.'class'.SLASH.'Database.class.php');

/**
 @version 2
 **/
class Entity
{
    public $debug = false,$pdo = null;
    public static $lastError = '';
    public static $lastResult = '';
    public static $lastQuery = '';

    public function __construct()
    {
        $this->connect();
    }

    public function connect()
    {
        $this->pdo = Database::instance();
        if (!isset($this->TABLE_NAME)) {
            $this->TABLE_NAME = strtolower(get_called_class());
        }
    }

    public function __toString()
    {
        foreach ($this->toArray() as $key => $value) {
            echo $key.' : '.$value.','.PHP_EOL;
        }
    }

    public static function debug()
    {
        return array(self::$lastQuery, self::$lastError, self::$lastResult);
    }

    public function __sleep()
    {
        return array_keys($this->toArray());
    }

    public function __wakeup()
    {
        $this->connect();
    }

    public function toArray()
    {
        $fields = array();
        foreach ($this->fields as $field => $type) {
            $fields[$field] = $this->$field;
        }

        return $fields;
    }

    public function fromArray($array)
    {
        foreach ($array as $field => $value) {
            $this->$field = $value;
        }
    }

    public function sgbdType($type)
    {
		$sgbd = BASE_SGBD;
        $types = $sgbd::types(); 
        return isset($types[$type]) ? $types[$type] : $types['default'];
    }

    public function closeDatabase()
    {
        //$this->close();
    }

    public static function tableName()
    {
        $class = get_called_class();
        $instance = new $class();

        return ENTITY_PREFIX.$instance->TABLE_NAME;
    }

        // GESTION SQL

        /**
         * Verifie l'existence de la table en base de donnée.
         *
         * @author Valentin CARRUESCO
         *
         * @category manipulation SQL
         *
         * @param <String> créé la table si elle n'existe pas
         *
         * @return true si la table existe, false dans le cas contraire
         */
        public static function checkTable($autocreate = false)
        {
            $class = get_called_class();
            $instance = new $class();
            $query = 'SELECT count(*) as numRows FROM sqlite_master WHERE type="table" AND name=?';
            $statement = $instance->customQuery($query, array($instance->tableName()));

            if ($statement != false) {
                $statement = $statement->fetchArray();
                if ($statement['numRows'] == 1) {
                    $return = true;
                }
            }
            if ($autocreate && !$return) {
                self::create();
            }

            return $return;
        }

    public static function install($classDirectory)
    {
        foreach (glob($classDirectory.SLASH.'*.class.php') as $file) {
            $infos = explode('.', basename($file));
            $class = array_shift($infos);
            require_once($classDirectory.SLASH.$class.'.class.php');
          
            if (!class_exists($class) || !method_exists($class, 'create') || $class == get_class()) {
                continue;
            }

            $class::create();
        }
    }
	
	 public static function uninstall($classDirectory)
    {
        foreach (glob($classDirectory.SLASH.'*.class.php') as $file) {

            $infos = explode('.', basename($file));
            $class = array_shift($infos);
            require_once($classDirectory.SLASH.$class.'.class.php');
            if (!class_exists($class) || !method_exists($class, 'create') || $class == get_class()) {
                continue;
            }
            $class::drop();
        }
    }

        /**
         * Methode de creation de l'entité.
         *
         * @author Valentin CARRUESCO
         *
         * @category manipulation SQL
         *
         * @return Aucun retour
         */
        public static function create()
        {
            $class = get_called_class();
            

            $instance = new $class();

			$fields = array();
			
			foreach ($instance->fields as $field => $type) 
                $fields[$field] =  $instance->sgbdType($type);
            
		
			$sgbd = BASE_SGBD;
			$sql = $sgbd::create();
			$query = Entity::render($sql,array(
					'table' => $instance->tableName(),
					'fields' => $fields
			));

            $instance->customExecute($query);
        }

    public static function drop()
    {
        $class = get_called_class();
        $instance = new $class();
		$sgbd = BASE_SGBD;
		$sql = $sgbd::drop();
		$query = Entity::render($sql,array(
					'table' => $instance->tableName()
		));
	
        $instance->customExecute($query);
    }

    /**
     * Methode d'insertion ou de modifications d'elements de l'entité.
     *
     * @author Valentin CARRUESCO
     *
     * @category manipulation SQL
     *
     * @param  Aucun
     *
     * @return Aucun retour
     */
    public function save()
    {
        $data = array();
        if (isset($this->id) && $this->id > 0) {
			
			$fields = array();
			$i = 0;
			foreach ($this->fields as $field => $type) {
                if ($type == 'key') continue;
				$data[':'.$i] = $this->{$field};
                if($type=='bool') $data[':'.$i] = $data[':'.$i] ? 1:0;
                $fields[$field] = ':'.$i;
				$i++;
            }
			$data[':id'] = $this->id;
			$sgbd = BASE_SGBD;
			$sql = $sgbd::update();
			$query = Entity::render($sql,array(
					'table' => $this->tableName(),
					'fields' => $fields,
					'filters' => array(':id='=>':id'),
			));
			
        } else {
			
			$fields = array();
			$i = 0;
			foreach ($this->fields as $field => $type) {
                if ($type == 'key') continue;
				$data[':'.$i] = $this->{$field};
                if($type=='bool') $data[':'.$i] = $data[':'.$i] ? 1:0;
                $fields[$field] = ':'.$i;
				$i++;
            }
		
			$sgbd = BASE_SGBD;
			$sql = $sgbd::insert();
			$query = Entity::render($sql,array(
					'table' => $this->tableName(),
					'fields' => $fields
			));
        }
	
        $this->customExecute($query, $data);

        $this->id = (!isset($this->id) || !(is_numeric($this->id)) ? $this->pdo->lastInsertId() : $this->id);
    }

    /**
     * Méthode de modification d'éléments de l'entité.
     *
     * @author Valentin CARRUESCO
     *
     * @category manipulation SQL
     *
     * @param <Array>  $colonnes=>$valeurs
     * @param <Array>  $colonnes           (WHERE) =>$valeurs (WHERE)
     * @param <String> $operation="="      definis le type d'operateur pour la requete select
     *
     * @return Aucun retour
     */
    public static function change($columns, $columns2 = array(), $operation = '=')
    {
        $class = get_called_class();
        $instance = new $class();
		
		$fields = array();
		$i = 0;
		foreach ($columns as $field => $value) {
			$data[':'.$i] = $value;
			$fields[$field] = ':'.$i;
			$i++;
		}
		
		$filters = array();
		$i = 0;
		foreach ($columns2 as $field => $value) {
			$data[':_'.$i] = $value;
			$filters[$field] = ':_'.$i;
			$i++;
		}

		$sgbd = BASE_SGBD;
		$sql = $sgbd::update();
		$query = Entity::render($sql,array(
				'table' => $instance->tableName(),
				'fields' => $fields,
				'filters' => $filters,
		));
	
        $instance->customExecute($query, $data);

    }

    /**
     * Méthode de selection de tous les elements de l'entité.
     *
     * @author Valentin CARRUESCO
     *
     * @category manipulation SQL
     *
     * @param <String> $ordre=null
     * @param <String> $limite=null
     *
     * @return <Array<Entity>> $Entity
     */
    public static function populate($order = null, $limit = null)
    {
        $results = self::loadAll(array(), $order, $limit);

        return $results;
    }

    /**
     * Méthode de selection multiple d'elements de l'entité.
     *
     * @author Valentin CARRUESCO
     *
     * @category manipulation SQL
     *
     * @param <Array>  $colonnes      (WHERE)
     * @param <Array>  $valeurs       (WHERE)
     * @param <String> $ordre=null
     * @param <String> $limite=null
     * @param <String> $operation="=" definis le type d'operateur pour la requete select
     *
     * @return <Array<Entity>> $Entity
     */
    public static function loadAll($columns = array(), $order = null, $limit = null, $selColumn = array('*'))
    {
			
		$values = array();
		
		$i=0;
		foreach($columns as $key=>$value){
			$tag = ':'.$i;
			$columns[$key] = $tag;
			$values[$tag] = $value;
			$i++;
		}
			
		$class = get_called_class();
		
        $instance = new $class();
		$data = array(
				'table' => $instance->tableName(),
				'selected' => $selColumn,
				'limit' =>  count($limit) == 0 ? null: $limit,
				'orderby'  => count($order) == 0 ? null: $order,
				'filter' =>  count($columns) == 0 ? null: $columns
		);
		$sgbd = BASE_SGBD;
		$sql = $sgbd::select();
		$sql = Entity::render($sql,$data);
        return $instance->customQuery($sql, $values, true);
    }


    public static function loadAllOnlyColumn($selColumn, $columns, $order = null, $limit = null)
    {
        $objects = self::loadAll($columns, $order, $limit, $operation, $selColumn);
        if (count($objects) == 0) {
            $objects = array();
        }

        return $objects;
    }

    /**
     * Méthode de selection unique d'élements de l'entité.
     *
     * @author Valentin CARRUESCO
     *
     * @category manipulation SQL
     *
     * @param <Array>  $colonnes      (WHERE)
     * @param <Array>  $valeurs       (WHERE)
     * @param <String> $operation="=" definis le type d'operateur pour la requete select
     *
     * @return <Entity> $Entity ou false si aucun objet n'est trouvé en base
     */
    public static function load($columns)
    {
        $objects = self::loadAll($columns, null, array('1'));
        if (!isset($objects[0])) {
            $objects[0] = false;
        }

        return $objects[0];
    }

    /**
     * Méthode de selection unique d'élements de l'entité.
     *
     * @author Valentin CARRUESCO
     *
     * @category manipulation SQL
     *
     * @param <Array>  $colonnes      (WHERE)
     * @param <Array>  $valeurs       (WHERE)
     * @param <String> $operation="=" definis le type d'operateur pour la requete select
     *
     * @return <Entity> $Entity ou false si aucun objet n'est trouvé en base
     */
    public static function getById($id, $operation = '=')
    {
        return self::load(array('id:'.$operation => $id));
    }

	public static function render($sql,$data=array()){
		
		//loop
		$sql = preg_replace_callback('/{{\:([^\/\:\?}]*)}}(.*?){{\/\:[^\/\:\?}]*}}/',function($matches) use ($data) {
			$key = $matches[1];
			$sqlTpl = $matches[2];
			
			$sql = '';
			if(isset($data[$key])){
				$i = 0;
				$values = $data[$key];
				foreach($values as $key=>$value){
					$i++;
					$last = $i == count($values);
					$operator = '=';
					if(strpos($key,':')!==false){
						$infos = explode(':',$key);
						$key = $infos[0];
						$operator = $infos[1];
					}
					
					$occurence = str_replace(array('{{key}}','{{value}}','{{operator}}'),array($key,$value,$operator),$sqlTpl); 
				
				
					
					$occurence = preg_replace_callback('/{{\;}}(.*?){{\/\;}}/',function($matches) use ($last){
						return $last? '': $matches[1];
					},$occurence);
					$sql.= $occurence;
					
				}
				return $sql;
			}
			return '';
		},$sql); 
		//conditions
		$sql = preg_replace_callback('/{{\?([^\/\:\?}]*)}}(.*?){{\/\?[^\/\:\?}]*}}/',function($matches) use ($data) {
			$key = $matches[1];
			$sql = $matches[2];
			return !isset($data[$key]) || (is_array($data[$key]) && count($data[$key])==0) ?'':$sql;
		},$sql); 
		//simple vars
		$sql = preg_replace_callback('/{{([^\/\:\;\?}]*)}}/',function($matches) use ($data) {
			$key = $matches[1];
			return isset($data[$key])?$data[$key]:'';
		},$sql); 
		
		return $sql;
	}
	
    /**
     * Methode de comptage des éléments de l'entité.
     *
     * @author Valentin CARRUESCO
     *
     * @category manipulation SQL
     * @return<Integer> nombre de ligne dans l'entité'
     */
    public static function rowCount($columns = null)
    {
		$class = get_called_class();
		$instance = new $class();
		$data = array(
			'table' => $class::tableName(),
			'selected' => 'id' ,
			'filter' =>  count($columns) == 0 ? null: $columns
		);
		$sgbd = BASE_SGBD;
		$sql = $sgbd::count();
		$execQuery = $instance->customQuery(Entity::render($sql,$data), array());
		$row = $execQuery->fetch();
		return $row['number'];
    }

        /**
         * Methode de définition de l'éxistence d'un moins un des éléments spécifiés en base.
         *
         * @author Valentin CARRUESCO
         *
         * @category manipulation SQL
         * @return<boolean> existe (true) ou non (false)
         */
        public static function exist($columns = null)
        {
            $result = self::rowCount($columns);

            return $result != 0;
        }

    public static function deleteById($id)
    {
        self::delete(array('id' => $id));
    }
        /**
         * Méthode de supression d'elements de l'entité.
         *
         * @author Valentin CARRUESCO
         *
         * @category manipulation SQL
         *
         * @param <Array>  $colonnes      (WHERE)
         * @param <Array>  $valeurs       (WHERE)
         * @param <String> $operation="=" definis le type d'operateur pour la requete select
         *
         * @return Aucun retour
         */
        public static function delete($columns, $limit = array())
        {
			$class = get_called_class();
			$instance = new $class();
			$data = array(
				'table' => $class::tableName(),
				'limit' =>  count($limit) == 0 ? null: $limit,
				'filter' =>  count($columns) == 0 ? null: $columns
			);
			$sgbd = BASE_SGBD;
			$sql = $sgbd::delete();
			
			return $instance->customExecute(Entity::render($sql,$data), array());
			
        }

    public function customExecute($query, $data = array())
    {
        self::$lastQuery = $query;
        $stm = $this->pdo->prepare($query);
        try {
            $stm->execute($data);
            //var_dump($query.' - '.json_encode($data, JSON_PRETTY_PRINT));
        } catch (Exception $e) {
            self::$lastError = $this->pdo->errorInfo();
			
            throw new Exception($e->getMessage().' - '.$e->getLine().' : '.$query.' - '.json_encode($data, JSON_PRETTY_PRINT));
        }
    }

    public static function staticQuery($query, $data = array(), $fill = false)
    {
        $class = get_called_class();
        $instance = new $class();
        return $instance->customQuery($query, $data, $fill);
    }

    public function customQuery($query, $data = array(), $fill = false)
    {
		
        self::$lastQuery = $query;
        $results = $this->pdo->prepare($query);
        $results->execute($data);

        if (!$results) {
            self::$lastError = $this->pdo->errorInfo();
			
            return false;
        } else {
            if (!$fill) {
                return $results;
            }

            $class = get_class($this);
            $objects = array();
			$results = $results->fetchAll();
			self::$lastResult = $results;
            foreach ($results as $queryReturn) {
				
                $object = new $class();
                foreach ($this->fields as $field => $type) {
                    if (isset($queryReturn[$field])) {
                        $object->{$field} = $queryReturn[$field];
                    }
                }
                $objects[] = $object;
                unset($object);
            }
			
			
            return $objects == null ? array()  : $objects;
        }
    }

    public function __get($name)
    {
        $pos = strpos($name, '_object');
        if ($pos !== false) {
            $field = strtolower(substr($name, 0, $pos));
            if (array_key_exists($field, $this->fields)) {
                $class = ucfirst($field);

                return $class::getById($this->{$field});
            }
        }
        throw new Exception('Attribut '.get_class($this)."->$name non existant");
    }
}
