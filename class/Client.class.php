<?php


class Client {
	public $socket;


    public  function  connect(){ 
        
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        $response = '';
        if ($this->socket !== false) {
            if (!socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1)) 
                throw new Exception(utf8_encode(socket_strerror(socket_last_error($this->socket))));
                
            $result = @socket_connect( $this->socket, '127.0.0.1', 9999);
            if(!$result){
                $this->socket = null;
                throw new Exception('Erreur connexion au serveur socket depuis yana-server, le serveur est il allumé ? '.utf8_encode(socket_strerror(socket_last_error())));
            }
        }

    }

    public   function  disconnect(){ 
        if($this->socket==null) return;
        socket_shutdown($this->socket);
        socket_close($this->socket);
        $this->socket = null;
    }

	public  function send($msg,$receive = false){ 
        if($this->socket==null) return;
            $in = json_encode($msg);
            $in .= '<EOF>';
            socket_write($this->socket, $in, strlen($in));
           
            if(!$receive) return $in;
            $in = '';
            $start = time();
            $go = false;
            socket_set_option($this->socket,SOL_SOCKET, SO_RCVTIMEO, array("sec"=>1, "usec"=>0));
           
            while (!$go) {
               $in .= @socket_read($this->socket, 2048);
               if((time()-$start) > 1) $go = true;
            }
            return $in;

    }


    public static function talk($parameter){
        $client = new self();
        $client->connect();
        $result =  $client->send(array("action"=>"TALK","parameter"=>$parameter));
        $client->disconnect();
        return $result;
    }
    
	public function sound($parameter){

        $client = new self();
        $client->connect();
		$result = $client->send(array("action"=>"SOUND","parameter"=>$parameter));
        $client->disconnect();
        return $result;
	}
	
	public function execute($parameter){
        $client = new self();
        $client->connect();
		$result = $client->send(array("action"=>"EXECUTE","parameter"=>$parameter));
        $client->disconnect();
        return $result;
	}
    public function emotion($emotion){
        $client = new self();
        $client->connect();
        $result = $client->send(array("action"=>"EMOTION","parameter"=>$emotion));
        $client->disconnect();
        return $result;
    }
    public function image($image){
        $client = new self();
        $client->connect();
        $result = $client->send(array("action"=>"IMAGE","parameter"=>$image));
        $client->disconnect();
        return $result;
    }


    // public static function connect() {
 
    // if(is_null(self::$instance)) {
    //     self::$instance = new Client();  
    // }
 
    //  return self::$instance;
    // }


}



?>
