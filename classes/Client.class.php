<?php


class Client {
	public $socket;


    public   function  connect(){ 
        try{
            //echo PHP_EOL.'Creation du socket d auto connexion';
            $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            $response = '';
            if ($this->socket !== false) {
                //echo PHP_EOL.'Connexion au serveur socket depuis yana-server';
                $result = @socket_connect( $this->socket, '127.0.0.1', 9999);
                return $result;
            }
        }catch(Exception $e){
            echo PHP_EOL.'Erreur connexion au serveur socket depuis yana-server';
        }
        return false;
    }

    public   function  disconnect(){ 
        socket_shutdown($this->socket);
        socket_close($this->socket);
        $this->socket = null;
    }

	public  function send($msg){ 
        //echo PHP_EOL.'Envois du message au client: '.$in;
        $in = json_encode($msg);
        $in .= '<EOF>';
        socket_write( $this->socket, $in, strlen($in));
        $int = '';
    }


    public function talk($parameter){
        //echo 'Execution envois de parole vers un client : '.$parameter;
        $this->send(array("action"=>"TALK","parameter"=>$parameter));
    }
    
	public function sound($parameter){
		$this->send(array("action"=>"SOUND","parameter"=>$parameter));
	}
	
	public function execute($parameter){
		$this->send(array("action"=>"EXECUTE","parameter"=>$parameter));
	}
    public function emotion($emotion){
        $this->send(array("action"=>"EMOTION","parameter"=>$emotion));
    }
    public function image($image){
        $this->send(array("action"=>"IMAGE","parameter"=>$image));
    }


    // public static function connect() {
 
    // if(is_null(self::$instance)) {
    //     self::$instance = new Client();  
    // }
 
    //  return self::$instance;
    // }


}



?>
