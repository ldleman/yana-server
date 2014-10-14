<?php
/**
* Manage application and plugins configurations with key/value pair
* 
* **nb:** It's possible to specify namespace in order to distinct global configuration to plugin custom configuration
* @author Idleman
* @category Database
* @license cc by nc sa
*/

class Configuration extends SQLiteEntity{

	protected $id,$key,$value,$confTab,$namespace;
	protected $TABLE_NAME = 'configuration';
	protected $CLASS_NAME = 'Configuration';
	protected $object_fields = 
	array(
		'id'=>'key',
		'key'=>'longstring',
		'value'=>'longstring'
	);

	function __construct(){
		parent::__construct();
	}

	/**
	* Get all configurations from database OR session if it was yet loaded
	* This function is called at start of program and global var '$conf' is filled with response, so use global $conf instead of call this function.
	* #### Example
	* ```php
	* $confs = Configuration::getAll();
	* var_dump($confs);
	* ```
	* @return array Array of configurations
	*/
	public function getAll(){

		if(!isset($_SESSION['configuration'])){
	
		$configurationManager = new Configuration();
		$configs = $configurationManager->populate();
		$confTab = array();

		foreach($configs as $config){
			
			$ns = 'conf';
			$key = $config->getKey();
			$infos  = explode(':',$key);
			if(count($infos) ==2){
				list($ns,$key) = $infos;
			}

			$this->confTab[$ns][$key] = $config->getValue();
		}

		$_SESSION['configuration'] = serialize($this->confTab);
		
		}else{
			$this->confTab = unserialize($_SESSION['configuration']);
		}
	}

	/**
	* Get configuration value from it key
	* #### Example
	* ```php
	* global $conf; // global var, contain configurations
	* echo $conf->get('myConfigKey'); // print myConfigKey value
	* ```
	* @param string configuration key
	* @param string configuration namespace (default is 'conf')
	* @return string valeur de la configuration
	*/
	public function get($key,$ns = 'conf'){
		return (isset($this->confTab[$ns][$key])?$this->confTab[$ns][$key]:'');
	}
	

	/**
	* Update or insert configuration value in database with specified key
	* #### Example
	* ```php
	* global $conf; // global var, contain configurations
	* echo $conf->put('myNewConfigKey','hello!'); //create configuration myNewConfigKey with value 'hello!'
	* echo $conf->put('myNewConfigKey','hello 2!'); //update configuration myNewConfigKey with value 'hello2!'
	* ```
	* @param string configuration key
	* @param string configuration value
	* @param string configuration namespace (default is 'conf')
	*/
	public function put($key,$value,$ns = 'conf'){
		$configurationManager = new Configuration();
		if (isset($this->confTab[$ns][$key])){
			$configurationManager->change(array('value'=>$value),array('key'=>$ns.':'.$key));
		} else {
			$configurationManager->add($key,$value,$ns);	
		}
		$this->confTab[$ns][$key] = $value;
		unset($_SESSION['configuration']);
	}

	/**
	* Remove configuration value in database with specified key
	* #### Example
	* ```php
	* global $conf; // global var, contain configurations
	* echo $conf->remove('myNewConfigKey'); //delete myNewConfigKey from 'conf' default namespace 
	* echo $conf->remove('myNewConfigKey','myCustomPluginConfig'); //delete myNewConfigKey from 'myCustomPluginConfig' namespace
	* ```
	* @param string configuration key
	* @param string configuration namespace (default is 'conf')
	*/
	public function remove($key,$ns = 'conf'){
		$configurationManager = new Configuration();
		if (isset($this->confTab[$ns][$key])){
			$configurationManager->delete(array('key'=>$ns.':'.$key));
		}
		unset($this->confTab[$ns][$key]);
		unset($_SESSION['configuration']);
	}
	
	private function add($key,$value,$ns = 'conf'){
		$config = new Configuration();
		$config->setKey($ns.':'.$key);
		$config->setValue($value);
		$config->save();
		$this->confTab[$ns][$key] = $value;
		unset($_SESSION['configuration']);
	}
	
	/**
	* Get current configuration id in database
	* @return int configuration id
	*/
	function getId(){
		return $this->id;
	}
	
	/**
	* Get current configuration key
	* @return string configuration key
	*/
	function getKey(){
		return $this->key;
	}

	/**
	* Set current configuration key
	* @param string configuration key
	*/
	function setKey($key){
		$this->key = $key;
	}

	/**
	* Get current configuration value
	* @return string configuration value
	*/
	function getValue(){
		return $this->value;
	}

	/**
	* Set current configuration value
	* @param string configuration value
	*/
	function setValue($value){
		$this->value = $value;
	}
	
	/**
	* Set current configuration namespace
	* @param string configuration namespace
	*/
	function setNameSpace($ns){
		$this->namespace = $ns;
	}
	
	/**
	* Get current configuration namespace
	* @return string configuration namespace
	*/
	function getNameSpace(){
		return $this->namespace;
	}



}

?>