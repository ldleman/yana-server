<?php


class Client {
	public $id;


	public static  function  send($msg){ 
		echo PHP_EOL.'Creation du socket d auto connexion';
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $response = '';
        if ($socket !== false) {
        	echo PHP_EOL.'Connexion au serveur socket depuis yana-server';
            $result = socket_connect($socket, '127.0.0.1', 9999);
            if ($result !== false) {
                echo PHP_EOL.'Envois du message au client: '.$in;
                $in = json_encode($msg);
                $in .= '<EOF>';
                socket_write($socket, $in, strlen($in));
                $out = '';
                echo PHP_EOL.'Déconnexion au serveur socket depuis yana-server';
                socket_shutdown($socket);
                socket_close($socket);
                $socket = null;
            }else{
            	$errorcode = socket_last_error();
    			$errormsg = socket_strerror($errorcode);
            	echo PHP_EOL.' Tentative de connexion au serveur socket depuis yana-server : ECHEC '.$errormsg;
            }
        }

        return $response;
    }


	public static function sound($parameter){
		Client::send(array("action"=>"SOUND","parameter"=>$parameter));
	}
	public static function talk($parameter){
		echo 'Execution envois de parole vers un client : '.$parameter;
		Client::send(array("action"=>"TALK","parameter"=>$parameter));
	}
	public static function execute($parameter){
		Client::send(array("action"=>"EXECUTE","parameter"=>$parameter));
	}
    public static function emotion($emotion){
        Client::send(array("action"=>"EMOTION","parameter"=>$emotion));
    }
    public static function image($image){
        Client::send(array("action"=>"IMAGE","parameter"=>$image));
    }


    public static function connect() {
 
    if(is_null(self::$instance)) {
        self::$instance = new Client();  
    }
 
     return self::$instance;
    }


}



?>