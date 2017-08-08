<?php
require_once(__DIR__.SLASH.'Relay.class.php');
/*
 @nom: WireRelay
 @auteur: Idleman (idleman@idleman.fr)
 @description:  Classe de gestion des relais filaires (prises, interrupteurs, coupe circuits ...)
 */

 class WireRelay extends Relay{
	public $pin;
	protected $TABLE_NAME = 'plugin_wire_relay';


	function __construct(){
		parent::__construct();
		$this->fields['pin'] = 'int';
		/*
		'reverse'=>'int',
		'pulse'=>'int',
		'oncommand'=>'string',
		'offcommand'=>'string',
		*/
	}

}

?>