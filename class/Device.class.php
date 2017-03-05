<?php
/**
* When devices (captors, interruptors...) and their values are declared here
* they can be used by all plugins of yana (maps, aggregators...)
* @author Idleman
* @category Hardware
* @license cc by nc sa
*/

class Device extends Entity{

	 const CAPTOR = 1;
	 const ACTUATOR = 2;
	 const BOTH = 3;
	 public $id,$label,$icon,$display,$state,$values,$location,$plugin,$actions,$type,$uid;
	 protected $TABLE_NAME = 'device';
	 protected $fields = 
	    array(
		    'id'=>'key',
		    'label'=>'string',
            'icon'=>'string',
			'display'=>'longstring',
			'state'=>'int',
			'values'=>'longstring',
			'location'=>'int',
			'plugin'=>'string',
			'actions'=>'longstring',
			'type'=>'int',
			'uid'=>'int'
	    );

	function __contruct(){
		$this->setValues(array());
	}
	public function setValue($key,$value){
		$values = $this->getValues();
		$values[$key] = $value;
		$this->setValues($values);
	}
	public function setValues($values){
		$this->values = json_encode($values);
	}
	public function getValues(){
		$values = json_decode($this->values,true);
		return is_array($values) ? $values : array();
	}
	public function getValue($key){
		$values = $this->getValues();
		return $values[$key];
	}
}
?>