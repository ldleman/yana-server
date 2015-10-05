<?php


class Client {
	public $id,$clients;

	function __construct(){
		$clients = array();
	}


	public static  function  send($msg){ 
		echo PHP_EOL.'Creation du socket d auto connexion';
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $response = '';
        if ($socket !== false) {
        	echo PHP_EOL.'Connexion au serveur socket depuis yana-server';
            $result = socket_connect($socket, '127.0.0.1', 9999);
            if ($result !== false) {
                $in = json_encode($msg);
                echo PHP_EOL.'Envois du message au client: '.$in;
                socket_write($socket, $in, strlen($in));
                $out = '';
               /* while ($out = socket_read($socket, 2048)) {
                   var_dump($out);
                   $response.= $out;
                }*/
            }else{
            	$errorcode = socket_last_error();
    			$errormsg = socket_strerror($errorcode);
            	echo PHP_EOL.' Tentative de connexion au serveur socket depuis yana-server : ECHEC '.$errormsg;
            }
            socket_close($socket);
        }
        return $response;
    }

	public function add($socket,$type){
		$this->clients[] = new ClientDevice($socket,$type,$location);
	}


	public function sound($parameter){
		Client::send(array("action"=>"SOUND","parameter"=>$parameter));
	}
	public static function talk($parameter){
		echo 'Execution envois de parole vers un client : '.$parameter;
		Client::send(array("action"=>"TALK","parameter"=>$parameter));
	}
	public function execute($parameter){
		Client::send(array("action"=>"EXECUTE","parameter"=>$parameter));
	}

}



?>