<?php


class Client {
	public $id,$clients;

	function __construct(){
		$clients = array();
	}

	public function add($socket,$type){
		$this->clients[] = new ClientDevice($socket,$type,$location);
	}

	public function configure(){
		return true;
	}
	public function sound(){
		return true;
	}
	public function talk(){
		return true;
	}
	public function execute(){
		return true;
	}
	public function send(){
		return true;
	}
}

class ClientDevice {
	public $id,$type,$socket,$location;

	function __construct($socket,$type,$location='global'){
		$this->$type = $type;
		$this->$socket = $socket;
	}



	public function suscribe($url,$token){
		//$this->save();
		return true;
	}
	public function configure($conf){
		return true;
	}
	public function sound(){
		return true;
	}
	public function talk(){
		return true;
	}
	public function execute(){
		return true;
	}
	public function send(){
		return true;
	}
}

?>