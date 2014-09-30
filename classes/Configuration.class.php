<?php

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

	public function get($key,$ns = 'conf'){
		return (isset($this->confTab[$ns][$key])?$this->confTab[$ns][$key]:'');
	}
	


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

	public function remove($key,$ns = 'conf'){
		$configurationManager = new Configuration();
		if (isset($this->confTab[$ns][$key])){
			$configurationManager->delete(array('key'=>$ns.':'.$key));
		}
		unset($this->confTab[$ns][$key]);
		unset($_SESSION['configuration']);
	}
	
	public function add($key,$value,$ns = 'conf'){
		$config = new Configuration();
		$config->setKey($ns.':'.$key);
		$config->setValue($value);
		$config->save();
		$this->confTab[$ns][$key] = $value;
		unset($_SESSION['configuration']);
	}
	
	function getId(){
		return $this->id;
	}

	function getKey(){
		return $this->key;
	}

	function setKey($key){
		$this->key = $key;
	}

	function getValue(){
		return $this->value;
	}

	function setValue($value){
		$this->value = $value;
	}
	function setNameSpace($ns){
		$this->namespace = $ns;
	}
	function getNameSpace(){
		return $this->namespace;
	}



}

?>