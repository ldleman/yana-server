<?php

		// 0. display graphic html
		// 1. Launch socket server
		// 2. Check if server.json is here (Y : 5, N : 3)
		// 3. wait with message "Veuillez appairer le serveur yana avec les informations : ip-> 192.168.0.x port->11000"
		// 4. get "suscribe" commande from yana server socket
		// 5. create server.json with sockets param yana-server ip and yana-server user token and return OK to yana-server
		// 6. get config value + speech commands from yana-server (can be reloaded by yana-server socket calling "reload_config" command)
		// 7. check startup parameters
		// 8. launch recognition
class Client extends SQLiteEntity{
	public $id,$name,$ip,$port;
	    protected $TABLE_NAME = 'client';
	    protected $CLASS_NAME = 'Client';
	    protected $object_fields = 
	    array(
		    'id'=>'key',
		    'name'=>'string',
            'ip'=>'string',
		    'port'=>'int'
	    );
	function __construct($ip=null,$port=null){
		 parent::__construct();
		 if(null!=$ip){
		 	$this->ip = $ip;
		 	$this->name = $ip;
		 }
		 if(null!=$port) $this->port = $port;
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