<?php
require_once(realpath(dirname(__FILE__)).'/common.php');
date_default_timezone_set('Europe/Paris');

class ClientSocket extends SocketServer {
	public $connected = array();

	function onDataReceived($socket,$data) {
			
			$this->handleData($this->connected[(int)$socket],$data);
			//$this->sendBroadcast("\r\n".$this->$connected[$socket]->name . ' : ' . $data);
			
	}
	function onClientConnected($socket) {
		$this->log('New client connected: ' . $socket);
		$client = new ClientDevice();
		$client->socket= $socket;
		$client->id= (int)$socket;
		$client->name = 'Client '.count($this->clients);
		$this->connected[(int)$socket] = $client;
		//$this->sendBroadcast($socket . ' entered the room');
	}

	function onClientDisconnected($socket) {
		$this->log($socket . ' disconnected');
		unset($this->connected[(int)$socket]);
		//$this->sendBroadcast($socket . ' left the room');
	}
	
	function handleData($client,$data){
		$this->log("Try to parse received data : ".$data);
		try{
		$_ = json_decode($data,true);

		if(!$_) throw new Exception("Unable to parse data : ".$data);
		

		if(!isset($_['action'])) $_['action'] = '';
		$this->log("Parsed action : ".$_['action']);

		switch($_['action']){
			case 'TALK':
				$this->talk($_['parameter']);
			break;
			case 'SOUND':
				$this->sound($_['parameter']);
			break;
			case 'EXECUTE':
				$this->execute($_['parameter']);
			break;
			case 'CLIENT_INFOS':

				$client->type = $_['type'];
				$client->location = $_['location'];
				$userManager = new User();
				$myUser = $userManager->load(array('token'=>$_['token']));
				if(isset($myUser) && $myUser!=false)
					$myUser->loadRight();
				$client->user =  (!$myUser?new User():$myUser);
				$this->log('setting infos '.$client->location.' for '.$client->name.' with user:'.$client->user->getLogin());
			break;
			case 'GET_SPEECH_COMMANDS':
				$response = array();
				Plugin::callHook("vocal_command", array(&$response,YANA_URL));
				$commands = array();
				foreach($response['commands'] as $command){
					unset($command['url']);
					$this->send($this->connected[$client->id]->socket,'{"action":"ADD_COMMAND","command":'.json_encode($command).'}');
				}
				$this->send($this->connected[$client->id]->socket,'{"action":"UPDATE_COMMANDS"}');
			break;
			case 'CATCH_COMMAND':
				$response = "";
				$this->log("Call listen hook (v2.0 plugins) with params ".$_['command']." > ".$_['text']." > ".$_['confidence']);
				Plugin::callHook('listen',array($_['command'],trim(str_replace($_['command'],'',$_['text'])),$_['confidence']));
				
			break;
			case '':
			default:
				//$this->talk("Coucou");
				//$this->sound("C:/poule.wav");
				//$this->execute("C:\Program Files (x86)\PuTTY\putty.exe");
				$this->log($client->name.'('.$client->type.') send '.$data);
			break;
		}

		$this->updateClient($client);
		}catch(Exception $e){
			$this->log("ERROR : ".$e->getMessage());
		}
		//system('php '.realpath(dirname(__FILE__)).'\action.php '.$json['action'],$out);
		//$this->send($socket,$out);
	}


	function updateClient($client){
	
		$this->connected[$client->id] = $client;
	}


	
	public function sound($message,$clients=array()){
		if(count($clients)==0)
			 $clients = $this->getByType('speak');
		

		foreach($clients as $client){
			$socket = $this->connected[$client->id]->socket;
			$this->send($socket,'{"action":"sound","file":"'.str_replace('\\','/',$message).'"}');
		}
	}
	
	public function talk($message,$clients=array()){

		$this->log("TALK : Try to send ".$message." to ".count($clients)." clients");
		if(count($clients)==0)
			 $clients = $this->getByType('speak');
		
		foreach($clients as $client){
			$socket = $this->connected[$client->id]->socket;
			$this->log("send ".'{"action":"talk","message":"'.$message.'"} to '.$client->name);
			$this->send($socket,'{"action":"talk","message":"'.$message.'"}');
		}
	}
	public function url($message,$clients=array()){
			echo "Envois de l\'url".$message;
		if(count($clients)==0)
			$clients = $this->getByType('speak');
			if(count($clients)==0) return;
			foreach($clients as $client){
			//$client = $clients[0];
			$socket = $this->connected[$client->id]->socket;
			$this->log("url ".'{"action":"url","url":"'.$message.'"} to '.$client->name);
			$this->send($socket,'{"action":"url","url":"'.$message.'"}');
		}
		
	}

	public function execute($message,$clients=array()){
		if(count($clients)==0)
			 $clients = $this->getByType('speak');
	
		foreach($clients as $client){
			$socket = $this->connected[$client->id]->socket;
			$this->log("send ".'{"action":"execute","command":"'.$message.'"} to '.$client->name);
			$this->send($socket,'{"action":"execute","command":"'.str_replace('\\','/',$message).'"}');
		}
	}
	
	public function getByType($type){
		$clients =array();
		foreach ($this->connected as $client) 
			if($client->type == $type) $clients[] = $client;
		return $clients;
	}


	private $lastMessage;
}

	$client = new ClientSocket('0.0.0.0',9999,20);
	$client->start();


	


class ClientDevice {
	public $id,$type,$socket,$location,$user;
}



/**
 * Class to handle a sockets server
 * It's abstract class so you need to create another class that will extends SocketServer to run your server
 * 
 * @author Cyril Mazur	www.cyrilmazur.com	twitter.com/CyrilMazur	facebook.com/CyrilMazur
 * @abstract
 */
abstract class SocketServer {
	/**
	 * The address the socket will be bound to
	 * @var string
	 */
	protected $address;
	
	/**
	 * The port the socket will be bound to
	 * @var int
	 */
	protected $port;
	
	/**
	 * The max number of clients authorized
	 * @var int
	 */
	protected $maxClients;
	
	/**
	 * Array containing all the connected clients
	 * @var array
	 */
	protected $clients;
	
	/**
	 * The master socket
	 * @var resource
	 */
	protected $master;
	
	/**
	 * Constructor
	 * @param string $address
	 * @param int $port
	 * @param int $maxClients
	 * @return SocketServer
	 */
	public function __construct($address,$port,$maxClients) {
		$this->address		= $address;
		$this->port			= $port;
		$this->maxClients	= $maxClients;
		$this->clients		= array();
	}
	
	/**
	 * Start the server
	 */
	public function start() {
		// flush all the output directly
		ob_implicit_flush();
		
		// create master socket
		$this->master = @socket_create(AF_INET, SOCK_STREAM, 0) or die($this->log('Could not create socket'));

		// to prevent: address already in use
		socket_set_option($this->master, SOL_SOCKET, SO_REUSEADDR, 1) or die($this->log('Could not set up SO_REUSEADDR'));
		
		// bind socket to port
		@socket_bind($this->master, $this->address, $this->port) or die($this->log('Could not bind to socket'));
		
		// start listening for connections
		socket_listen($this->master) or die($this->log('Could not set up socket listener'));
		
		$this->log('Server started on ' . $this->address . ':' . $this->port);
		
		// infinite loop
		while(true) {
			// build the array of sockets to select
			$read	= array_merge(array($this->master),$this->clients);
			
			$write = NULL;
			$except = NULL;
			$tv_sec = NULL;
			// if no socket has changed its status, continue the loop
			socket_select($read,$write,$except,$tv_sec);
			
			// if the master's status changed, it means a new client would like to connect
			if (in_array($this->master,$read)) {
				
				// if we didn't reach the maximum amount of connected clients
				if (sizeof($this->clients) < $this->maxClients) {
					
					// attempt to create a new socket
					$socket = socket_accept($this->master);
					
					// if socket created successfuly, add it to the clients array and write message log
					if ($socket !== false) {
						$this->clients[] = $socket;
						
						if (socket_getpeername($socket,$ip)) {
							$this->log('New client connected: ' . $socket . ' (' . $ip . ')');
						} else {
							$this->log('New client connected: ' . $socket);
						}
						
						$this->onClientConnected($socket);
						
					// else display error message to the log console
					} else {
						$this->log('Impossible to connect new client',true);
					}
				
				// else tell the client that there is not place available and display error message to the log console
				} else {
					$socket = socket_accept($this->master);
					socket_write($socket,'Max clients reached. Retry later.' . chr(0));
					socket_close($socket);
					
					$this->log('Impossible to connect new client: maxClients reached');
				}
				
				if (sizeof($read) == 1)
					continue;
			}
			
			// foreach client that is ready to be read
			foreach($read as $client) {
				
				// we don't read data from the master socket
				if ($client != $this->master) {
					
					// read input
					$input = @socket_read($client, 1024, PHP_BINARY_READ);
					
					// if socket_read() returned false, the client has been disconnected
					if (strlen($input) == 0) {
						// disconnect client
						$this->disconnect($client);
						
						// custom method called
						$this->onClientDisconnected($client);
					
					// else, we received a normal message
					} else {
						$input = trim($input);
						
						// special case of a domain policy file request
						if ($input == '<policy-file-request/>') {
							$cmd = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><cross-domain-policy xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\"http://www.adobe.com/xml/schemas/PolicyFileSocket.xsd\"><allow-access-from domain=\"*\" to-ports=\"*\" secure=\"false\" /><site-control permitted-cross-domain-policies=\"master-only\" /></cross-domain-policy>";
							
							$this->log('Policy file requested by ' . $client);
							socket_write($client,$cmd . chr(0));
							
						// normal case, standard message
						} else {
							// custom method called
							$this->onDataReceived($client,$input);
						}
					}
				}
			}
		}
	}
	
	/**
	 * Stop the server: disconnect all the coonected clients, close the master socket
	 */
	public function stop() {
		foreach($this->clients as $client) {
			socket_close($client);
		}
		
		$this->clients = array();
		
		socket_close($this->master);
	}
	
	/**
	 * Disconnect a client
	 * @param resource $client
	 * @return bool
	 */
	protected function disconnect($client) {
		// close socket
		socket_close($client);
		
		// unset variable in the clients array
		$key = array_keys($this->clients,$client);
		unset($this->clients[$key[0]]);
		
		$this->log('Client disconnected: ' . $client);
		
		return true;
	}
	
	/**
	 * Send data to a client
	 * @param resource $client
	 * @param string $data
	 * @return bool
	 */
	protected function send($client,$data) {

		return @socket_write($client, $data . chr(0));
	}
	
	/**
	 * Send data to everybody
	 * @param string $data
	 * @return bool
	 */
	protected function sendBroadcast($data) {
		$return = true;
		foreach($this->clients as $client) {
			$return = $return && socket_write($client, $data . chr(0));
		}
		
		return $return;
	}
	
	/**
	 * Method called after a value had been read
	 * @abstract
	 * @param resource $socket
	 * @param string $data
	 */
	abstract protected function onDataReceived($socket,$data);
	
	/**
	 * Method called after a new client is connected
	 * @param resource $socket
	 */
	abstract protected function onClientConnected($socket);
	
	/**
	 * Method called after a new client is disconnected
	 * @param resource $socket
	 */
	abstract protected function onClientDisconnected($socket);
	
	/**
	 * Write log messages to the console
	 * @param string $message
	 * @param bool $socketError
	 */
	public function log($message,$socketError = false) {
		echo '[' . date('d/m/Y H:i:s') . '] ' . $message;
		
		if ($socketError) {
			$errNo	= socket_last_error();
			$errMsg	= socket_strerror($errNo);
			
			echo ' : #' . $errNo . ' ' . $errMsg;
		}
		
		echo "\n";
	}
}
?>
